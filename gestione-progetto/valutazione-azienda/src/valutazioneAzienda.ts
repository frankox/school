export type BilancioAnnuale = {
  redditoLordo: number;
  imposte: number;
};

export type ValutazioneAzienda = {
  redditiNetti: number[];
  redditoAnnoCorrente: number;
  redditiProspettici: number[];
  redditoMedioProspettico: number;
  valoreAzienda: number;
};

/**
 * I cinque bilanci sono in ordine cronologico, dal più vecchio al più recente.
 * `anniProspettici` comprende l'anno corrente, il primo senza rendiconto.
 * `tassoCapitalizzazione` è in forma decimale: 0.10 significa 10%.
 */
export function valutaAzienda(
  bilanci: readonly BilancioAnnuale[],
  anniProspettici: number,
  tassoCapitalizzazione: number,
): ValutazioneAzienda {
  // TODO: leggere il README e completare la funzione facendo passare i test.
  throw new Error('Funzione da implementare');
}
