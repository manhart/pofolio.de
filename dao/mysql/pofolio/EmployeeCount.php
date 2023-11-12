<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Split.php created on 05.11.23, 16:36.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class EmployeeCount extends PofolioDAO
{
    protected static ?string $tableName = 'EmployeeCount';

    protected array $pk = [
        'idEmployeeCount'
    ];

    protected array $columns = [
        'idEmployeeCount',
        'idStock',
        'CIK',
        'acceptanceTime',
        'periodOfReport',
        'formType',
        'filingDate',
        'employeeCount',
        'source',
    ];

    public function exists(int $idStock, \DateTimeInterface $date): bool
    {
        $filter = [
            ['idStock', 'equal', $idStock],
            ['filingDate', 'equal', $date],
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}