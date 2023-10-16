<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * StockDividendCalendar.php created on 16.10.23, 23:57.
 */

namespace pofolio\classes\FMP\Response;

class StockDividendCalendar extends Response
{
    protected static string $url = 'v3/stock_dividend_calendar';

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getLabel(): string
    {
        return $this->getResponseValue('label');
    }

    public function getAdjDividend(): float
    {
        return $this->getResponseValueAsFloat('adjDividend');
    }

    public function getDividend(): float
    {
        return $this->getResponseValueAsFloat('dividend');
    }

    public function getRecordDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('recordDate');
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('paymentDate');
    }

    public function getDeclarationDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('declarationDate');
    }
}