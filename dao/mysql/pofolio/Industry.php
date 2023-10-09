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

class Industry extends PofolioDAO
{
    protected static ?string $tableName = 'Industry';

    protected array $pk = [
        'idIndustry'
    ];

    protected array $columns = [
        'idIndustry',
        'industry',
    ];

    public function exists(string $industry): bool
    {
        $filter = [
            ['industry', 'equal', $industry]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}