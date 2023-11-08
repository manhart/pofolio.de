<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Sector.php created on 09.10.23, 17:01.
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
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}