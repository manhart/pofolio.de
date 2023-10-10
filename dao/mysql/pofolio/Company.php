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

class Company extends PofolioDAO
{
    protected static ?string $tableName = 'Company';

    protected array $pk = [
        'idStock'
    ];

    protected array $columns = [
        'idStock',
        'companyName',
        'website',
        'description',
        'ceo',
        'fullTimeEmployees',
        'address',
        'city',
        'zip',
        'state',
        'phone',
        'image',
        'fiscalYearEndDay',
        'fiscalYearEndMonth',
        'registrantName',
        'taxIdentificationNumber',
        'stateLocation',
        'stateOfIncorporation',
        'sicCode',
        'dcf'
    ];

    public function exists(int $idStock): bool
    {
        $filter = [
            ['idStock', 'equal', $idStock]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}