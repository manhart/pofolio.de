<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Dividend.php created on 26.09.23, 00:15.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class ShareFloat extends PofolioDAO
{
    protected static ?string $tableName = 'ShareFloat';

    protected array $pk = [
        'idShareFloat'
    ];

    protected array $columns = [
        'idShareFloat',
        'idStock',
        'date',
        'freeFloat',
        'floatShares',
        'outstandingShares',
        'source',
    ];

    public function exists(int $idStock, \DateTimeInterface $date): bool
    {
        $filter = [
            ['idStock', 'equal', $idStock],
            ['date', 'equal', $date],
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}