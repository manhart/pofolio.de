<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * StockSplitCalendar.php created on 05.11.23, 15:18.
 */

namespace pofolio\classes\FMP\Response;

class StockSplitCalendar extends Response
{
    protected static string $url = 'v3/stock_split_calendar';

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

    public function getNumerator(): float
    {
        return $this->getResponseValueAsFloat('numerator');
    }

    public function getDenominator(): int
    {
        return $this->getResponseValueAsInt('denominator');
    }
}