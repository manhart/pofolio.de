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

class Dividend extends PofolioDAO
{
    protected static ?string $tableName = 'Dividend';

    protected array $pk = [
        'idDividend'
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