<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * SplitCalendar.php created on 05.11.23, 16:36.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class SplitCalendar extends PofolioDAO
{
    protected static ?string $tableName = 'SplitCalendar';

    protected array $pk = [
        'idSplitCalendar'
    ];

    protected array $columns = [
        'idSplitCalendar',
        'idStock',
        'date',
        'numerator',
        'denominator',
        'updated',
        'created',
    ];

    public function exists(int $idStock, \DateTimeInterface $date): bool
    {
        $filter = [
            ['idStock', 'equal', $idStock],
            ['date', 'equal', $date],
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}