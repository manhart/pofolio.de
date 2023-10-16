<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Profile.php created on 27.09.23, 20:32.
 */

namespace pofolio\classes\FMP\Response;

class HistoricalPrice extends Response
{
    protected static string $url = 'v3/historical-price-full';

    private string $symbol;

    public function __construct(array $response)
    {
        $this->symbol = $response['symbol'] ?? '';
        parent::__construct($response['historical'] ?? []);
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getOpen(): float
    {
        return $this->getResponseValueAsFloat('open');
    }

    public function getHigh(): float
    {
        return $this->getResponseValueAsFloat('high');
    }

    public function getLow(): float
    {
        return $this->getResponseValueAsFloat('low');
    }

    public function getClose(): float
    {
        return $this->getResponseValueAsFloat('close');
    }

    public function getAdjClose(): float
    {
        return $this->getResponseValueAsFloat('adjClose');
    }

    public function getVolume(): int
    {
        return $this->getResponseValueAsInt('volume');
    }

    public function getUnadjustedVolume(): int
    {
        return $this->getResponseValueAsInt('unadjustedVolume');
    }

    public function getChange(): float
    {
        return $this->getResponseValueAsFloat('change');
    }

    public function getChangePercent(): float
    {
        return $this->getResponseValueAsFloat('changePercent');
    }

    public function getVwap(): float
    {
        return $this->getResponseValueAsFloat('vwap');
    }

    public function getLabel(): string
    {
        return $this->getResponseValue('label');
    }

    public function getChangeOverTime(): float
    {
        return $this->getResponseValueAsFloat('changeOverTime');
    }
}