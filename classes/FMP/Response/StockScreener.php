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

class StockScreener extends Response
{
    protected static string $url = 'v3/stock-screener';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getCompanyName(): string
    {
        return $this->getResponseValueAsString('companyName');
    }

    public function getMarketCap(): int
    {
        return $this->getResponseValueAsInt('marketCap');
    }

    public function getSector(): string
    {
        return $this->getResponseValueAsString('sector');
    }

    public function getIndustry(): string
    {
        return $this->getResponseValueAsString('industry');
    }

    public function getBeta(): float
    {
        return $this->getResponseValueAsFloat('beta');
    }

    public function getPrice(): float
    {
        return $this->getResponseValueAsFloat('price');
    }

    public function getLastAnnualDividend(): float
    {
        return $this->getResponseValueAsFloat('lastAnnualDividend');
    }

    public function getVolume(): int
    {
        return $this->getResponseValueAsInt('volume');
    }

    public function getExchange(): string
    {
        return $this->getResponseValueAsString('exchange');
    }

    public function getExchangeShortName(): string
    {
        return $this->getResponseValueAsString('exchangeShortName');
    }

    public function getCountry(): string
    {
        return $this->getResponseValueAsString('country');
    }

    public function getIsEtf(): bool
    {
        return $this->getResponseValueAsBool('isEtf');
    }

    public function getIsActivelyTrading(): bool
    {
        return $this->getResponseValueAsBool('isActivelyTrading');
    }
}