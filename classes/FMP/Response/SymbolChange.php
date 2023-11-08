<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * SymbolChange.php created on 08.11.23, 21:06.
 */

namespace pofolio\classes\FMP\Response;

class SymbolChange extends Response
{
    protected static string $url = 'v4/symbol_change';

    public function getDate(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getName(): string
    {
        return $this->getResponseValueAsString('name');
    }

    public function getOldSymbol(): string
    {
        return $this->getResponseValueAsString('oldSymbol');
    }

    public function getNewSymbol(): string
    {
        return $this->getResponseValueAsString('newSymbol');
    }
}