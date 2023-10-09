<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Stock.php created on 26.09.23, 00:15.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class Stock extends PofolioDAO
{
    protected static ?string $tableName = 'Stock';

    protected array $pk = [
        'idStock'
    ];

    protected array $columns = [
        'idStock',
        'symbol',
        'type',
        'name',
        'idExchange',
        'tradeable',
    ];

    public function exists(string $symbol): bool
    {
        $filter = [
            ['symbol', 'equal', $symbol]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}