<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * BalanceSheetStatement.php created on 16.10.23, 21:45.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class BalanceSheetStatement extends PofolioDAO
{
    protected static ?string $tableName = 'BalanceSheetStatement';

    protected array $pk = [
        'idBalanceSheetStatement'
    ];

    protected array $columns = [
        'idBalanceSheetStatement',
        'idStock',
        'date',
        'idReportedCurrency',
        'fillingDate',
        'acceptedDate',
        'calendarYear',
        'timePeriod',
        'cashAndCashEquivalents',
        'shortTermInvestments',
        'cashAndShortTermInvestments',
        'netReceivables',
        'inventory',
        'otherCurrentAssets',
        'totalCurrentAssets',
        'propertyPlantEquipmentNet',
        'goodwill',
        'intangibleAssets',
        'goodwillAndIntangibleAssets',
        'longTermInvestments',
        'taxAssets',
        'otherNonCurrentAssets',
        'totalNonCurrentAssets',
        'otherAssets',
        'totalAssets',
        'accountPayables',
        'shortTermDebt',
        'taxPayables',
        'deferredRevenue',
        'otherCurrentLiabilities',
        'totalCurrentLiabilities',
        'longTermDebt',
        'deferredRevenueNonCurrent',
        'deferredTaxLiabilitiesNonCurrent',
        'otherNonCurrentLiabilities',
        'totalNonCurrentLiabilities',
        'otherLiabilities',
        'capitalLeaseObligations',
        'totalLiabilities',
        'preferredStock',
        'commonStock',
        'retainedEarnings',
        'accumulatedOtherComprehensiveIncomeLoss',
        'othertotalStockholdersEquity',
        'totalStockholdersEquity',
        'totalEquity',
        'totalLiabilitiesAndStockholdersEquity',
        'minorityInterest',
        'totalLiabilitiesAndTotalEquity',
        'totalInvestments',
        'totalDebt',
        'netDebt',
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
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}