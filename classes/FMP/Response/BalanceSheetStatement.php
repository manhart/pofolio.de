<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * BalanceSheetStatement.php created on 16.10.23, 22:05.
 */

namespace pofolio\classes\FMP\Response;

class BalanceSheetStatement extends Response
{
    protected static string $url = 'v3/balance-sheet-statement';

    public const PERIOD_QUARTER = 'quarter',
        PERIOD_ANNUAL = 'annual';

    public function getDate(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getReportedCurrency(): string
    {
        return $this->getResponseValueAsString('reportedCurrency');
    }

    public function getCIK(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getFillingDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('fillingDate');
    }

    public function getAcceptedDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('acceptedDate');
    }

    public function getCalendarYear(): ?int
    {
        return $this->getResponseValueAsInt('calendarYear') ?: null;
    }

    public function getPeriod(): string
    {
        return $this->getResponseValueAsString('period');
    }

    public function getCashAndCashEquivalents(): int
    {
        return $this->getResponseValueAsInt('cashAndCashEquivalents');
    }

    public function getShortTermInvestments(): int
    {
        return $this->getResponseValueAsInt('shortTermInvestments');
    }

    public function getCashAndShortTermInvestments(): int
    {
        return $this->getResponseValueAsInt('cashAndShortTermInvestments');
    }

    public function getNetReceivables(): int
    {
        return $this->getResponseValueAsInt('netReceivables');
    }

    public function getInventory(): int
    {
        return $this->getResponseValueAsInt('inventory');
    }

    public function getOtherCurrentAssets(): int
    {
        return $this->getResponseValueAsInt('otherCurrentAssets');
    }

    public function getTotalCurrentAssets(): int
    {
        return $this->getResponseValueAsInt('totalCurrentAssets');
    }

    public function getPropertyPlantEquipmentNet(): int
    {
        return $this->getResponseValueAsInt('propertyPlantEquipmentNet');
    }

    public function getGoodwill(): int
    {
        return $this->getResponseValueAsInt('goodwill');
    }

    public function getIntangibleAssets(): int
    {
        return $this->getResponseValueAsInt('intangibleAssets');
    }

    public function getGoodwillAndIntangibleAssets(): int
    {
        return $this->getResponseValueAsInt('goodwillAndIntangibleAssets');
    }

    public function getLongTermInvestments(): int
    {
        return $this->getResponseValueAsInt('longTermInvestments');
    }

    public function getTaxAssets(): int
    {
        return $this->getResponseValueAsInt('taxAssets');
    }

    public function getOtherNonCurrentAssets(): int
    {
        return $this->getResponseValueAsInt('otherNonCurrentAssets');
    }

    public function getTotalNonCurrentAssets(): int
    {
        return $this->getResponseValueAsInt('totalNonCurrentAssets');
    }

    public function getOtherAssets(): int
    {
        return $this->getResponseValueAsInt('otherAssets');
    }

    public function getTotalAssets(): int
    {
        return $this->getResponseValueAsInt('totalAssets');
    }

    public function getAccountPayables(): int
    {
        return $this->getResponseValueAsInt('accountPayables');
    }

    public function getShortTermDebt(): int
    {
        return $this->getResponseValueAsInt('shortTermDebt');
    }

    public function getTaxPayables(): int
    {
        return $this->getResponseValueAsInt('taxPayables');
    }

    public function getDeferredRevenue(): int
    {
        return $this->getResponseValueAsInt('deferredRevenue');
    }

    public function getOtherCurrentLiabilities(): int
    {
        return $this->getResponseValueAsInt('otherCurrentLiabilities');
    }

    public function getTotalCurrentLiabilities(): int
    {
        return $this->getResponseValueAsInt('totalCurrentLiabilities');
    }

    public function getLongTermDebt(): int
    {
        return $this->getResponseValueAsInt('longTermDebt');
    }

    public function getDeferredRevenueNonCurrent(): int
    {
        return $this->getResponseValueAsInt('deferredRevenueNonCurrent');
    }

    public function getDeferredTaxLiabilitiesNonCurrent(): int
    {
        return $this->getResponseValueAsInt('deferredTaxLiabilitiesNonCurrent');
    }

    public function getOtherNonCurrentLiabilities(): int
    {
        return $this->getResponseValueAsInt('otherNonCurrentLiabilities');
    }

    public function getTotalNonCurrentLiabilities(): int
    {
        return $this->getResponseValueAsInt('totalNonCurrentLiabilities');
    }

    public function getOtherLiabilities(): int
    {
        return $this->getResponseValueAsInt('otherLiabilities');
    }

    public function getCapitalLeaseObligations(): int
    {
        return $this->getResponseValueAsInt('capitalLeaseObligations');
    }

    public function getTotalLiabilities(): int
    {
        return $this->getResponseValueAsInt('totalLiabilities');
    }

    public function getPreferredStock(): int
    {
        return $this->getResponseValueAsInt('preferredStock');
    }

    public function getCommonStock(): int
    {
        return $this->getResponseValueAsInt('commonStock');
    }

    public function getRetainedEarnings(): int
    {
        return $this->getResponseValueAsInt('retainedEarnings');
    }

    public function getAccumulatedOtherComprehensiveIncomeLoss(): int
    {
        return $this->getResponseValueAsInt('accumulatedOtherComprehensiveIncomeLoss');
    }

    public function getOthertotalStockholdersEquity(): int
    {
        return $this->getResponseValueAsInt('othertotalStockholdersEquity');
    }

    public function getTotalStockholdersEquity(): int
    {
        return $this->getResponseValueAsInt('totalStockholdersEquity');
    }

    public function getTotalEquity(): int
    {
        return $this->getResponseValueAsInt('totalEquity');
    }

    public function getTotalLiabilitiesAndStockholdersEquity(): int
    {
        return $this->getResponseValueAsInt('totalLiabilitiesAndStockholdersEquity');
    }

    public function getMinorityInterest(): int
    {
        return $this->getResponseValueAsInt('minorityInterest');
    }

    public function getTotalLiabilitiesAndTotalEquity(): int
    {
        return $this->getResponseValueAsInt('totalLiabilitiesAndTotalEquity');
    }

    public function getTotalInvestments(): int
    {
        return $this->getResponseValueAsInt('totalInvestments');
    }

    public function getTotalDebt(): int
    {
        return $this->getResponseValueAsInt('totalDebt');
    }

    public function getNetDebt(): int
    {
        return $this->getResponseValueAsInt('netDebt');
    }

    public function getLink(): string
    {
        return $this->getResponseValueAsString('link');
    }

    public function getFinalLink(): string
    {
        return $this->getResponseValueAsString('finalLink');
    }
}