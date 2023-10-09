<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * StockList.php created on 27.09.23, 20:32.
 */

namespace pofolio\classes\FMP\Response;

class StockList extends Response
{
    protected static string $url = 'v3/stock/list';

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getExchange(): string
    {
        return $this->getResponseValueAsString('exchange');
    }

    public function getExchangeShortName(): string
    {
        return $this->getResponseValueAsString('exchangeShortName');
    }

    public function getPrice(): float
    {
        return $this->getResponseValueAsFloat('price');
    }

    public function getName(): string
    {
        return $this->getResponseValueAsString('name');
    }

    public function getType(): string
    {
        return $this->getResponseValueAsString('type');
    }
}