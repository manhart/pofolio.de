<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * DividendCalendar.php created on 17.10.23, 00:00.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class DividendCalendar extends PofolioDAO
{
    protected static ?string $tableName = 'DividendCalendar';

    protected array $pk = [
        'idDividendCalendar'
    ];

    protected array $columns = [
        'idStock',
        'date',
        'adjDividend',
        'dividend',
        'recordDate',
        'paymentDate',
        'declarationDate',
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