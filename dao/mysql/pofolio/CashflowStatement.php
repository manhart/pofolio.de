<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CashflowStatementStatement.php created on 03.11.23, 14:44.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class CashflowStatement extends PofolioDAO
{
    protected static ?string $tableName = 'CashflowStatement';

    protected array $pk = [
        'idCashflowStatement'
    ];

    protected array $columns = [
        'idCashflowStatement',
        'idStock',
        'date',
        'idReportedCurrency',
        'CIK',
        'fillingDate',
        'acceptedDate',
        'calendarYear',
        'timePeriod',
        'periodOrder',
        'netIncome',
        'depreciationAndAmortization',
        'deferredIncomeTax',
        'stockBasedCompensation',
        'changeInWorkingCapital',
        'accountsReceivables',
        'inventory',
        'accountsPayables',
        'otherWorkingCapital',
        'otherNonCashItems',
        'netCashProvidedByOperatingActivities',
        'investmentsInPropertyPlantAndEquipment',
        'acquisitionsNet',
        'purchasesOfInvestments',
        'salesMaturitiesOfInvestments',
        'otherInvestingActivites',
        'netCashUsedForInvestingActivites',
        'debtRepayment',
        'commonStockIssued',
        'commonStockRepurchased',
        'dividendsPaid',
        'otherFinancingActivites',
        'netCashUsedProvidedByFinancingActivities',
        'effectOfForexChangesOnCash',
        'netChangeInCash',
        'cashAtEndOfPeriod',
        'cashAtBeginningOfPeriod',
        'operatingCashFlow',
        'capitalExpenditure',
        'freeCashFlow',
        'link',
        'finalLink',
        'updated',
        'created',
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
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}