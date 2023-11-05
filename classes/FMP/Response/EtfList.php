<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * EtfList.php created on 05.11.23, 20:50.
 */

namespace pofolio\classes\FMP\Response;

class EtfList extends Response
{
    protected static string $url = 'v3/etf/list';

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