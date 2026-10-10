import assert from 'node:assert/strict';
import test from 'node:test';
import { getCompanyValuation, type AnnualStatement } from '../src/companyEvaluation.js';

function assertClose(actual?: number, expected?: number): void {
  assert.notEqual(actual, undefined, 'actual is undefined');
  assert.notEqual(expected, undefined, 'expected is undefined');
  if (actual === undefined || expected === undefined) {
    return;
  }

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

test('rejects fewer than five statements', () => {
  for (const count of [0, 4]) {
    assert.throws(() => getCompanyValuation(variableStatements.slice(0, count), 3, 0.10), RangeError);
  }
});

test('rejects projection years that are not positive integers', () => {
  for (const years of [0, -1, 1.5, Infinity, -Infinity, NaN]) {
    assert.throws(() => getCompanyValuation(variableStatements, years, 0.10), RangeError);
  }
});

test('rejects capitalization rates that are not positive finite numbers', () => {
  for (const rate of [0, -0.1, Infinity, -Infinity, NaN]) {
    assert.throws(() => getCompanyValuation(variableStatements, 3, rate), RangeError);
  }
});

test('rejects negative or non-finite gross income', () => {
  for (const grossIncome of [-1, Infinity, -Infinity, NaN]) {
    const statements = [{ grossIncome, taxes: 0 }, ...variableStatements.slice(1)];
    assert.throws(() => getCompanyValuation(statements, 3, 0.10), RangeError);
  }
});

test('rejects negative, excessive, or non-finite taxes', () => {
  for (const taxes of [-1, 101, Infinity, -Infinity, NaN]) {
    const statements = [{ grossIncome: 100, taxes }, ...variableStatements.slice(1)];
    assert.throws(() => getCompanyValuation(statements, 3, 0.10), RangeError);
  }
});

test('validates statements beyond the first five', () => {
  const statements = [...variableStatements, { grossIncome: 100, taxes: 101 }];
  assert.throws(() => getCompanyValuation(statements, 3, 0.10), RangeError);
});

test('accepts zero income and taxes equal to gross income', () => {
  const statements = [
    { grossIncome: 0, taxes: 0 },
    { grossIncome: 100, taxes: 100 },
    ...variableStatements.slice(2),
  ];
  const result = getCompanyValuation(statements, 1, 0.10);
  assert.deepEqual(result.historicalNetIncomes?.slice(0, 2), [0, 0]);
});

test('subtracts taxes from income for all five historical years', () => {
  const result = getCompanyValuation(variableStatements, 3, 0.10);
  assert.deepEqual(result.historicalNetIncomes, [100_000, 150_000, 120_000, 180_000, 200_000]);
});

test('fits a linear trend using all five years to predict the current year', () => {
  const result = getCompanyValuation(variableStatements, 3, 0.10);
  // The net income series is not linear: using only the endpoints gives another result.
  assertClose(result.currentYearIncome, 219_000);
});

test('projects n years starting with the current year and averages them', () => {
  const result = getCompanyValuation(variableStatements, 3, 0.10);
  assert.equal(result.projectedIncomes?.length, 3);
  result.projectedIncomes.forEach((income, index) => {
    assertClose(income, [219_000, 242_000, 265_000][index]);
  });
  assertClose(result.averageProjectedIncome, 242_000);
});

test('capitalizes the average projected income using the given rate', () => {
  const result = getCompanyValuation(variableStatements, 3, 0.10);
  assertClose(result.companyValue, 2_420_000);
});

test('one projection year makes the average equal to the current-year forecast', () => {
  const result = getCompanyValuation(variableStatements, 1, 0.20);
  assert.equal(result.projectedIncomes?.length, 1);
  assertClose(result.averageProjectedIncome, 219_000);
  assertClose(result.companyValue, 1_095_000);
});

test('a constant series has no projected growth', () => {
  const statements = Array.from({ length: 5 }, () => ({ grossIncome: 120_000, taxes: 20_000 }));
  const result = getCompanyValuation(statements, 4, 0.10);
  result.projectedIncomes?.forEach((income) => assertClose(income, 100_000));
  assertClose(result.averageProjectedIncome, 100_000);
});

test('also handles a declining trend', () => {
  const statements = [200_000, 180_000, 160_000, 140_000, 120_000]
    .map((grossIncome) => ({ grossIncome, taxes: 0 }));
  const result = getCompanyValuation(statements, 2, 0.20);
  result.projectedIncomes?.forEach((income, index) => {
    assertClose(income, [100_000, 80_000][index]);
  });
  assertClose(result.averageProjectedIncome, 90_000);
  assertClose(result.companyValue, 450_000);
});

test('uses all six statements to fit the trend and forecast from the seventh year', () => {
  const statements = [...variableStatements, { grossIncome: 290_000, taxes: 50_000 }];
  const result = getCompanyValuation(statements, 2, 0.10);
  assert.deepEqual(result.historicalNetIncomes, [100_000, 150_000, 120_000, 180_000, 200_000, 240_000]);
  assertClose(result.currentYearIncome, 256_000);
  assert.deepEqual(result.projectedIncomes, [256_000, 282_000]);
  assertClose(result.averageProjectedIncome, 269_000);
  assertClose(result.companyValue, 2_690_000);
});
