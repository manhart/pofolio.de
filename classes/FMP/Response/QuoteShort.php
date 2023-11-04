<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * QuoteShort.php created on 03.11.23, 20:58.
 */

namespace classes\FMP\Response;

use pofolio\classes\FMP\Response\Response;

class QuoteShort extends Response
{
    protected static string $url = 'v3/quote-short';

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getPrice(): float
    {
        return $this->getResponseValueAsFloat('price');
    }

    public function getVolume(): int
    {
        return $this->getResponseValueAsInt('volume');
    }
}