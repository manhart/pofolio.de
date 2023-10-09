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

class Sector extends PofolioDAO
{
    protected static ?string $tableName = 'Sector';

    protected array $pk = [
        'idSector'
    ];

    protected array $columns = [
        'idSector',
        'sector',
    ];

    public function exists(string $sector): bool
    {
        $filter = [
            ['sector', 'equal', $sector]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}