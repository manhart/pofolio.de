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

class SIC extends PofolioDAO
{
    protected static ?string $tableName = 'SIC';

    protected array $pk = [
        'sicCode'
    ];

    protected array $columns = [
        'sicCode',
        'sicGroup',
        'sicDescription',
    ];

    public function exists(int $sicCode): bool
    {
        $filter = [
            ['sicCode', 'equal', $sicCode]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}