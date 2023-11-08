<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * IncomeStatement.php created on 15.10.23, 19:38.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class IncomeStatement extends PofolioDAO
{
    protected static ?string $tableName = 'IncomeStatement';

    protected array $pk = [
        'idIncomeStatement'
    ];

    protected array $columns = [
        'idIncomeStatement',
        'idStock',
        'date',
        'idReportedCurrency',
        'fillingDate',
        'acceptedDate',
        'calendarYear',
        'timePeriod',
        'revenue',
        'costOfRevenue',
        'grossProfit',
        'grossProfitRatio',
        'researchAndDevelopmentExpenses',
        'generalAndAdministrativeExpenses',
        'sellingAndMarketingExpenses',
        'sellingGeneralAndAdministrativeExpenses',
        'otherExpenses',
        'operatingExpenses',
        'costAndExpenses',
        'interestIncome',
        'interestExpense',
        'depreciationAndAmortization',
        'EBITDA',
        'EBITDARatio',
        'operatingIncome',
        'operatingIncomeRatio',
        'totalOtherIncomeExpensesNet',
        'incomeBeforeTax',
        'incomeBeforeTaxRatio',
        'incomeTaxExpense',
        'netIncome',
        'netIncomeRatio',
        'EPS',
        'EPSDiluted',
        'weightedAverageShsOut',
        'weightedAverageShsOutDil',
        'link',
        'finalLink',
    ];

    public const PERIOD_FISCAL_YEAR = 'FY',
        PERIOD_QUARTER1 = 'Q1',
        PERIOD_QUARTER2 = 'Q2',
        PERIOD_QUARTER3 = 'Q3',
        PERIOD_QUARTER4 = 'Q4';

    public function exists(int $idStock, string $calendarYear, string $period = self::PERIOD_FISCAL_YEAR): bool
    {
        $filter = [
            ['idStock', 'equal', $idStock],
            ['calendarYear', 'equal', $calendarYear],
            ['timePeriod', 'equal', $period]
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}