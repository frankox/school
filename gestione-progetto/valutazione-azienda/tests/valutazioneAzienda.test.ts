import assert from 'node:assert/strict';
import test from 'node:test';
import { valutaAzienda, type BilancioAnnuale } from '../src/valutazioneAzienda.js';

function vicino(attuale: number, atteso: number): void {
  assert.ok(
    Math.abs(attuale - atteso) < 1e-8,
    `Atteso ${atteso}, ottenuto ${attuale}`,
  );
}

const bilanciVariabili: BilancioAnnuale[] = [
  { redditoLordo: 125_000, imposte: 25_000 },
  { redditoLordo: 190_000, imposte: 40_000 },
  { redditoLordo: 150_000, imposte: 30_000 },
  { redditoLordo: 220_000, imposte: 40_000 },
  { redditoLordo: 250_000, imposte: 50_000 },
];

test('sottrae le imposte dai redditi dei cinque anni', () => {
  const risultato = valutaAzienda(bilanciVariabili, 3, 0.10);
  assert.deepEqual(risultato.redditiNetti, [100_000, 150_000, 120_000, 180_000, 200_000]);
});

test('usa la tendenza lineare su tutti e cinque gli anni per stimare l\'anno corrente', () => {
  const risultato = valutaAzienda(bilanciVariabili, 3, 0.10);
  // La serie netta non è una retta: usare solo il primo e l'ultimo anno dà un risultato diverso.
  vicino(risultato.redditoAnnoCorrente, 219_000);
});

test('prevede n anni a partire dall\'anno corrente e ne calcola la media', () => {
  const risultato = valutaAzienda(bilanciVariabili, 3, 0.10);
  assert.equal(risultato.redditiProspettici.length, 3);
  risultato.redditiProspettici.forEach((reddito, indice) => {
    vicino(reddito, [219_000, 242_000, 265_000][indice]);
  });
  vicino(risultato.redditoMedioProspettico, 242_000);
});

test('capitalizza il reddito medio prospettico usando il tasso dato', () => {
  const risultato = valutaAzienda(bilanciVariabili, 3, 0.10);
  vicino(risultato.valoreAzienda, 2_420_000);
});

test('con un solo anno prospettico la media coincide con la previsione corrente', () => {
  const risultato = valutaAzienda(bilanciVariabili, 1, 0.20);
  assert.equal(risultato.redditiProspettici.length, 1);
  vicino(risultato.redditoMedioProspettico, 219_000);
  vicino(risultato.valoreAzienda, 1_095_000);
});

test('gestisce una serie costante senza inventare crescita', () => {
  const bilanci = Array.from({ length: 5 }, () => ({ redditoLordo: 120_000, imposte: 20_000 }));
  const risultato = valutaAzienda(bilanci, 4, 0.10);
  risultato.redditiProspettici.forEach((reddito) => vicino(reddito, 100_000));
  vicino(risultato.redditoMedioProspettico, 100_000);
});

test('gestisce anche una tendenza decrescente', () => {
  const bilanci = [200_000, 180_000, 160_000, 140_000, 120_000]
    .map((redditoLordo) => ({ redditoLordo, imposte: 0 }));
  const risultato = valutaAzienda(bilanci, 2, 0.20);
  risultato.redditiProspettici.forEach((reddito, indice) => {
    vicino(reddito, [100_000, 80_000][indice]);
  });
  vicino(risultato.redditoMedioProspettico, 90_000);
  vicino(risultato.valoreAzienda, 450_000);
});

test('rifiuta un numero di bilanci diverso da cinque', () => {
  assert.throws(() => valutaAzienda(bilanciVariabili.slice(1), 3, 0.10), RangeError);
  assert.throws(() => valutaAzienda([...bilanciVariabili, bilanciVariabili[0]], 3, 0.10), RangeError);
});

test('rifiuta redditi e imposte non validi', () => {
  const conPrimoBilancio = (bilancio: BilancioAnnuale) =>
    valutaAzienda([bilancio, ...bilanciVariabili.slice(1)], 3, 0.10);

  assert.throws(() => conPrimoBilancio({ redditoLordo: -1, imposte: 0 }), RangeError);
  assert.throws(() => conPrimoBilancio({ redditoLordo: Infinity, imposte: 0 }), RangeError);
  assert.throws(() => conPrimoBilancio({ redditoLordo: 100, imposte: -1 }), RangeError);
  assert.throws(() => conPrimoBilancio({ redditoLordo: 100, imposte: 101 }), RangeError);
  assert.throws(() => conPrimoBilancio({ redditoLordo: 100, imposte: NaN }), RangeError);
});

test('rifiuta un orizzonte o un tasso di capitalizzazione non validi', () => {
  for (const anni of [0, -1, 1.5, Infinity]) {
    assert.throws(() => valutaAzienda(bilanciVariabili, anni, 0.10), RangeError);
  }
  for (const tasso of [0, -0.1, Infinity, NaN]) {
    assert.throws(() => valutaAzienda(bilanciVariabili, 3, tasso), RangeError);
  }
});
