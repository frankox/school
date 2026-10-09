import assert from 'node:assert/strict';
import test from 'node:test';
import { valueCompany, type AnnualStatement } from '../src/companyEvaluation.js';

function assertClose(actual: number, expected: number): void {
  assert.ok(
    Math.abs(actual - expected) < 1e-8,
    `Expected ${expected}, got ${actual}`,
  );
}

const variableStatements: AnnualStatement[] = [
  { grossIncome: 125_000, taxes: 25_000 },
  { grossIncome: 190_000, taxes: 40_000 },
  { grossIncome: 150_000, taxes: 30_000 },
  { grossIncome: 220_000, taxes: 40_000 },
  { grossIncome: 250_000, taxes: 50_000 },
];

test('subtracts taxes from income for all five historical years', () => {
  const result = valueCompany(variableStatements, 3, 0.10);
  assert.deepEqual(result.historicalNetIncomes, [100_000, 150_000, 120_000, 180_000, 200_000]);
});

test('fits a linear trend using all five years to predict the current year', () => {
  const result = valueCompany(variableStatements, 3, 0.10);
  // The net income series is not linear: using only the endpoints gives another result.
  assertClose(result.currentYearIncome, 219_000);
});

test('projects n years starting with the current year and averages them', () => {
  const result = valueCompany(variableStatements, 3, 0.10);
  assert.equal(result.projectedIncomes.length, 3);
  result.projectedIncomes.forEach((income, index) => {
    assertClose(income, [219_000, 242_000, 265_000][index]);
  });
  assertClose(result.averageProjectedIncome, 242_000);
});

test('capitalizes the average projected income using the given rate', () => {
  const result = valueCompany(variableStatements, 3, 0.10);
  assertClose(result.companyValue, 2_420_000);
});

test('one projection year makes the average equal to the current-year forecast', () => {
  const result = valueCompany(variableStatements, 1, 0.20);
  assert.equal(result.projectedIncomes.length, 1);
  assertClose(result.averageProjectedIncome, 219_000);
  assertClose(result.companyValue, 1_095_000);
});

test('a constant series has no projected growth', () => {
  const statements = Array.from({ length: 5 }, () => ({ grossIncome: 120_000, taxes: 20_000 }));
  const result = valueCompany(statements, 4, 0.10);
  result.projectedIncomes.forEach((income) => assertClose(income, 100_000));
  assertClose(result.averageProjectedIncome, 100_000);
});

test('also handles a declining trend', () => {
  const statements = [200_000, 180_000, 160_000, 140_000, 120_000]
    .map((grossIncome) => ({ grossIncome, taxes: 0 }));
  const result = valueCompany(statements, 2, 0.20);
  result.projectedIncomes.forEach((income, index) => {
    assertClose(income, [100_000, 80_000][index]);
  });
  assertClose(result.averageProjectedIncome, 90_000);
  assertClose(result.companyValue, 450_000);
});

test('rejects any statement count other than five', () => {
  assert.throws(() => valueCompany(variableStatements.slice(1), 3, 0.10), RangeError);
  assert.throws(() => valueCompany([...variableStatements, variableStatements[0]], 3, 0.10), RangeError);
});

test('rejects invalid income and taxes', () => {
  const withFirstStatement = (statement: AnnualStatement) =>
    valueCompany([statement, ...variableStatements.slice(1)], 3, 0.10);

  assert.throws(() => withFirstStatement({ grossIncome: -1, taxes: 0 }), RangeError);
  assert.throws(() => withFirstStatement({ grossIncome: Infinity, taxes: 0 }), RangeError);
  assert.throws(() => withFirstStatement({ grossIncome: 100, taxes: -1 }), RangeError);
  assert.throws(() => withFirstStatement({ grossIncome: 100, taxes: 101 }), RangeError);
  assert.throws(() => withFirstStatement({ grossIncome: 100, taxes: NaN }), RangeError);
});

test('rejects an invalid projection period or capitalization rate', () => {
  for (const years of [0, -1, 1.5, Infinity]) {
    assert.throws(() => valueCompany(variableStatements, years, 0.10), RangeError);
  }
  for (const rate of [0, -0.1, Infinity, NaN]) {
    assert.throws(() => valueCompany(variableStatements, 3, rate), RangeError);
  }
});
