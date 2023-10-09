<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Exchange.php created on 26.09.23, 00:18.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class Exchange extends PofolioDAO
{
    protected static ?string $tableName = 'Exchange';

    protected array $pk = [
        'idExchange'
    ];

    protected array $columns = [
        'idExchange',
        'exchange',
        'exchangeShortName',
    ];

    public function exists(string $exchangeShortName): bool
    {
        $filter = [
            ['exchangeShortName', 'equal', $exchangeShortName]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}