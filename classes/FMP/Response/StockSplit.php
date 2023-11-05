<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * StockSplit.php created on 05.11.23, 15:15.
 */

namespace pofolio\classes\FMP\Response;

class StockSplit extends Response
{
    protected static string $url = 'v3/historical-price-full/stock_split';

    private string $symbol;

    public function __construct(array $response)
    {
        $this->symbol = $response['symbol'];
        parent::__construct($response['historical']);
    }

    public function getSymbol(): string
    {
        return $this->symbol;
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