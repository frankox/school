@echo off
setlocal
set "SCRIPT_DIR=%~dp0"
where php >nul 2>nul
if %ERRORLEVEL%==0 (
    php "%SCRIPT_DIR%tests\run.php"
) else if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" "%SCRIPT_DIR%tests\run.php"
) else (
    echo PHP non trovato. Controlla XAMPP o il Path.
    exit /b 1
)
