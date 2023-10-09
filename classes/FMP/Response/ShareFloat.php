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

class ShareFloat extends Response
{
    protected static string $url = 'v4/shares_float';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getFreeFloat(): ?float
    {
        return $this->getResponseValueAsFloat('freeFloat');
    }

    public function getFloatShares(): ?int
    {
        return $this->getResponseValueAsInt('floatShares');
    }

    public function getOutstandingShares(): ?int
    {
        return $this->getResponseValueAsInt('outstandingShares');
    }

    public function getSource(): ?string
    {
        return $this->getResponseValue('source');
    }
}