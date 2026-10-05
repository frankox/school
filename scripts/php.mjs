import { spawn, spawnSync } from 'node:child_process';
import { createWriteStream, existsSync, mkdirSync, renameSync, rmSync } from 'node:fs';
import { get } from 'node:https';
import { delimiter, dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(fileURLToPath(new URL('..', import.meta.url)));
const exercises = {
  1: '01-csv-to-sql-api',
  2: '02-create-student-api',
  3: '03-json-batch-transaction',
  4: '04-order-transaction',
  example: '00-example-books-api',
};

function inspect(binary, args) {
  return spawnSync(binary, args, { encoding: 'utf8', windowsHide: true });
}

function findPhp() {
  const candidates = process.env.PHP_BIN
    ? [process.env.PHP_BIN]
    : process.platform === 'win32'
      ? ['C:\\xampp\\php\\php.exe', 'php']
      : ['php'];
  for (const candidate of candidates) {
    if (inspect(candidate, ['-v']).status !== 0) continue;
    const path = inspect(candidate, ['-r', 'echo PHP_BINARY;']).stdout?.trim();
    return path && existsSync(path) ? path : candidate;
  }
  throw new Error('PHP non trovato. Imposta PHP_BIN con il percorso del php.exe di XAMPP.');
}

function phpDetails(php) {
  const result = inspect(php, ['-r', 'echo json_encode([PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_ZTS, PHP_INT_SIZE, ini_get("extension_dir")]);']);
  if (result.status !== 0) throw new Error(result.stderr || 'Impossibile leggere la configurazione PHP.');
  return JSON.parse(result.stdout);
}

function download(url, destination) {
  return new Promise((resolveDownload, reject) => {
    const temporary = `${destination}.part`;
    const request = get(url, (response) => {
      if (response.statusCode !== 200) {
        response.resume();
        reject(new Error(`Download Xdebug fallito: HTTP ${response.statusCode}.`));
        return;
      }
      const file = createWriteStream(temporary);
      response.pipe(file);
      file.on('finish', () => file.close(() => {
        renameSync(temporary, destination);
        resolveDownload();
      }));
      file.on('error', reject);
    });
    request.on('error', reject);
  }).catch((error) => {
    rmSync(`${destination}.part`, { force: true });
    throw error;
  });
}

async function xdebugOptions(php) {
  if (inspect(php, ['-r', 'echo extension_loaded("xdebug") ? "yes" : "no";']).stdout?.trim() === 'yes') return [];
  const [major, minor, threadSafe, wordSize, extensionDir] = phpDetails(php);
  const suffix = process.platform === 'win32' ? 'dll' : 'so';
  const candidates = [
    process.env.XDEBUG_EXTENSION,
    join(extensionDir, `xdebug.${suffix}`),
    join(extensionDir, `php_xdebug.${suffix}`),
  ].filter(Boolean);
  for (const candidate of candidates) {
    if (!existsSync(candidate)) continue;
    const check = inspect(php, ['-d', `zend_extension=${candidate}`, '-r', 'echo extension_loaded("xdebug") ? "yes" : "no";']);
    if (check.status === 0 && check.stdout?.trim() === 'yes') {
      return ['-d', `zend_extension=${candidate}`];
    }
  }
  if (process.platform === 'win32' && major === 8 && minor === 0 && wordSize === 8) {
    const filename = `php_xdebug-3.4.7-8.0-${threadSafe ? 'ts' : 'nts'}-vs16-x86_64.dll`;
    const local = join(root, '.runtime', 'xdebug', filename);
    if (!existsSync(local)) {
      mkdirSync(dirname(local), { recursive: true });
      console.log('Preparo Xdebug per PHP 8.0 nella cartella del progetto...');
      await download(`https://xdebug.org/files/${filename}`, local);
    }
    return ['-d', `zend_extension=${local}`];
  }
  throw new Error('Xdebug non trovato per questo PHP. Installa la versione adatta o imposta XDEBUG_EXTENSION.');
}

function run(binary, args, cwd = root, environment = {}) {
  const pathKey = Object.keys(process.env).find((key) => key.toLowerCase() === 'path') ?? 'PATH';
  const env = { ...process.env, ...environment, [pathKey]: `${dirname(binary)}${delimiter}${process.env[pathKey] ?? ''}` };
  const child = spawn(binary, args, { cwd, env, stdio: 'inherit', windowsHide: true });
  child.on('error', (error) => {
    console.error(error.message);
    process.exitCode = 1;
  });
  child.on('exit', (code, signal) => {
    process.exitCode = signal ? 1 : (code ?? 1);
  });
}

function startDockerDatabase(directory) {
  const args = ['compose', 'up', '-d', '--wait', 'db'];
  const first = spawnSync('docker', args, { cwd: directory, encoding: 'utf8', windowsHide: true });
  if (first.status === 0) {
    process.stdout.write(first.stdout);
    process.stderr.write(first.stderr);
    return;
  }

  const output = `${first.stdout ?? ''}${first.stderr ?? ''}`;
  if (process.platform !== 'win32' && /permission denied.*docker API/i.test(output)) {
    console.log('Docker richiede sudo su questo sistema. Avvio solo il database con privilegi amministrativi...');
    const retry = spawnSync('sudo', ['docker', ...args], { cwd: directory, stdio: 'inherit' });
    if (retry.status === 0) return;
    throw new Error(`Impossibile avviare MariaDB con sudo. ${retry.error?.message ?? 'Controlla il messaggio di Docker qui sopra.'}`);
  }

  process.stdout.write(first.stdout ?? '');
  process.stderr.write(first.stderr ?? '');
  throw new Error(`Impossibile avviare MariaDB con Docker Compose. ${first.error?.message ?? 'Controlla Docker e il suo accesso al daemon.'}`);
}

try {
  const [mode, exercise, ...extra] = process.argv.slice(2);
  const php = findPhp();
  if (mode === 'doctor') {
    if (exercise) throw new Error('doctor non accetta opzioni.');
    console.log(`PHP: ${php}`);
    process.stdout.write(inspect(php, ['-v']).stdout);
  } else if (mode === 'ide') {
    if (!exercise) throw new Error('Manca il file PHP da avviare.');
    const options = await xdebugOptions(php);
    const check = inspect(php, [...options, '-r', 'echo extension_loaded("xdebug") ? "yes" : "no";']);
    if (check.status !== 0 || check.stdout?.trim() !== 'yes') {
      throw new Error(`Xdebug non si carica con questo PHP. ${check.stderr?.trim() ?? ''}`);
    }
    run(php, [...options, '-d', 'xdebug.mode=debug', '-d', 'xdebug.start_with_request=yes', exercise, ...extra], process.cwd());
  } else if (mode === 'serve-docker') {
    if (exercise !== '2' || extra.length) throw new Error('serve-docker è disponibile solo per l’esercizio 2.');
    const directory = join(root, 'info', 'API', exercises[2]);
    startDockerDatabase(directory);

    const driverCheck = ['-r', 'echo extension_loaded("pdo_mysql") ? "yes" : "no";'];
    const driverOptions = inspect(php, driverCheck).stdout?.trim() === 'yes' ? [] : ['-d', 'extension=pdo_mysql'];
    if (inspect(php, [...driverOptions, ...driverCheck]).stdout?.trim() !== 'yes') {
      throw new Error('Il PHP selezionato non riesce a caricare pdo_mysql. Controlla PHP_BIN o installa il modulo.');
    }

    const port = process.env.PORT || '8000';
    if (!/^\d+$/.test(port)) throw new Error('PORT deve essere un numero.');
    run(php, [...driverOptions, '-S', `127.0.0.1:${port}`, '-t', join(directory, 'public')], directory, {
      DB_DSN: 'mysql:host=127.0.0.1;port=3307;dbname=school_ex02;charset=utf8mb4',
      DB_USER: 'school_api',
      DB_PASSWORD: 'school_dev',
    });
  } else if (mode === 'test' || mode === 'serve') {
    if (extra.length || (exercise && exercise.startsWith('-'))) throw new Error('Questo comando non accetta opzioni di debug. Usa F5 in VS Code.');
    const folder = exercises[exercise];
    if (!folder || (mode === 'test' && exercise === 'example')) {
      throw new Error('Esercizio non valido. Usa 1, 2, 3 o 4; per il server puoi usare anche example.');
    }
    const directory = join(root, 'info', 'API', folder);
    if (mode === 'test') {
      run(php, [join(directory, 'tests', 'run.php')], directory);
    } else {
      const port = process.env.PORT || '8000';
      if (!/^\d+$/.test(port)) throw new Error('PORT deve essere un numero.');
      run(php, ['-S', `localhost:${port}`, '-t', join(directory, 'public')], directory);
    }
  } else {
    throw new Error('Usa npm run doctor, npm run test:2, npm run serve:2 o npm run serve:2:docker. Per il debug dei test usa F5 in VS Code.');
  }
} catch (error) {
  console.error(error.message);
  process.exitCode = 1;
}
