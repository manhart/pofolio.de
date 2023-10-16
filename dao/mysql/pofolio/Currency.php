<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Currency.php created on 09.10.23, 17:01.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class Currency extends PofolioDAO
{
    protected static ?string $tableName = 'Currency';

    protected array $pk = [
        'idCurrency'
    ];

    protected array $columns = [
        'idCurrency',
        'currency',
    ];

    public function exists(string $currency): bool
    {
        $filter = [
            ['currency', 'equal', $currency]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}