export type AnnualStatement = {
  grossIncome: number;
  taxes: number;
};

export type CompanyValuation = {
  historicalNetIncomes: number[];
  currentYearIncome: number;
  projectedIncomes: number[];
  averageProjectedIncome: number;
  companyValue: number;
};

/**
 * At least five statements are ordered from oldest to newest.
 * `projectionYears` includes the current year, the first without a statement.
 * `capitalizationRate` is a decimal: 0.10 means 10%.
 */
export function getCompanyValuation(
  statements: readonly AnnualStatement[],
  projectionYears: number,
  capitalizationRate: number,
): CompanyValuation {
  // TODO: Read the README and implement the function until all tests pass.
  throw new Error('Function not implemented');
}
