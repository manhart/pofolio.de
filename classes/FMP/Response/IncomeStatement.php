<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * IncomeStatement.php created on 27.09.23, 20:32.
 */

namespace pofolio\classes\FMP\Response;

class IncomeStatement extends Response
{
    protected static string $url = 'v3/income-statement';

    public const PERIOD_QUARTER = 'quarter';
    public const PERIOD_ANNUAL = 'annual';

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
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

    public function getRevenue(): float
    {
        return $this->getResponseValueAsFloat('revenue');
    }

    public function getCostOfRevenue(): float
    {
        return $this->getResponseValueAsFloat('costOfRevenue');
    }

    public function getGrossProfit(): float
    {
        return $this->getResponseValueAsFloat('grossProfit');
    }

    public function getGrossProfitRatio(): float
    {
        return $this->getResponseValueAsFloat('grossProfitRatio');
    }

    public function getResearchAndDevelopmentExpenses(): float
    {
        return $this->getResponseValueAsFloat('researchAndDevelopmentExpenses');
    }

    public function getGeneralAndAdministrativeExpenses(): float
    {
        return $this->getResponseValueAsFloat('generalAndAdministrativeExpenses');
    }

    public function getSellingAndMarketingExpenses(): float
    {
        return $this->getResponseValueAsFloat('sellingAndMarketingExpenses');
    }

    public function getSellingGeneralAndAdministrativeExpenses(): float
    {
        return $this->getResponseValueAsFloat('sellingGeneralAndAdministrativeExpenses');
    }

    public function getOtherExpenses(): float
    {
        return $this->getResponseValueAsFloat('otherExpenses');
    }

    public function getOperatingExpenses(): float
    {
        return $this->getResponseValueAsFloat('operatingExpenses');
    }

    public function getCostAndExpenses(): float
    {
        return $this->getResponseValueAsFloat('costAndExpenses');
    }

    public function getInterestIncome(): float
    {
        return $this->getResponseValueAsFloat('interestIncome');
    }

    public function getInterestExpense(): float
    {
        return $this->getResponseValueAsFloat('interestExpense');
    }

    public function getDepreciationAndAmortization(): float
    {
        return $this->getResponseValueAsFloat('depreciationAndAmortization');
    }

    public function getEBITDA(): float
    {
        return $this->getResponseValueAsFloat('ebitda');
    }

    public function getEBITDARatio(): float
    {
        return $this->getResponseValueAsFloat('ebitdaratio');
    }

    public function getOperatingIncome(): float
    {
        return $this->getResponseValueAsFloat('operatingIncome');
    }

    public function getOperatingIncomeRatio(): float
    {
        return $this->getResponseValueAsFloat('operatingIncomeRatio');
    }

    public function getTotalOtherIncomeExpensesNet(): float
    {
        return $this->getResponseValueAsFloat('totalOtherIncomeExpensesNet');
    }

    public function getIncomeBeforeTax(): float
    {
        return $this->getResponseValueAsFloat('incomeBeforeTax');
    }

    public function getIncomeBeforeTaxRatio(): float
    {
        return $this->getResponseValueAsFloat('incomeBeforeTaxRatio');
    }

    public function getIncomeTaxExpense(): float
    {
        return $this->getResponseValueAsFloat('incomeTaxExpense');
    }

    public function getNetIncome(): float
    {
        return $this->getResponseValueAsFloat('netIncome');
    }

    public function getNetIncomeRatio(): float
    {
        return $this->getResponseValueAsFloat('netIncomeRatio');
    }

    public function getEPS(): float
    {
        return $this->getResponseValueAsFloat('eps');
    }

    public function getEPSDiluted(): float
    {
        return $this->getResponseValueAsFloat('epsdiluted');
    }

    public function getWeightedAverageShsOut(): float
    {
        return $this->getResponseValueAsFloat('weightedAverageShsOut');
    }

    public function getWeightedAverageShsOutDil(): float
    {
        return $this->getResponseValueAsFloat('weightedAverageShsOutDil');
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