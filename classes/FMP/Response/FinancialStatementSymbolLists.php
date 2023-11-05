<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * FinancialStatementSymbolLists.php created on 05.11.23, 20:55.
 */

namespace pofolio\classes\FMP\Response;

class FinancialStatementSymbolLists extends Response
{
    protected static string $url = 'v3/financial-statement-symbol-lists';

    private array $symbols;

    public function __construct(array $response)
    {
        $this->symbols = $response;

        // For the implemented interfaces, an associative array with the key 'symbol' within an indexed array is needed.
        $symbolAssocArray = array_map(static fn($item) => ['symbol' => $item], $this->symbols);
        parent::__construct($symbolAssocArray);
    }

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getSymbols(): array
    {
        return $this->symbols;
    }
}