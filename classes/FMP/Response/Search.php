<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Search.php created on 08.11.23, 19:31.
 */

namespace pofolio\classes\FMP\Response;

class Search extends Response
{
    protected static string $url = 'v3/search';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getName(): string
    {
        return $this->getResponseValueAsString('name');
    }

    public function getCurrency(): string
    {
        return $this->getResponseValueAsString('currency');
    }

    public function getStockExchange(): string
    {
        return $this->getResponseValueAsString('stockExchange');
    }

    public function getExchangeShortName(): string
    {
        return $this->getResponseValueAsString('exchangeShortName');
    }
}