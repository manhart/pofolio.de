<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Country.php created on 09.10.23, 22:57.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class Country extends PofolioDAO
{
    protected static ?string $tableName = 'Country';

    protected array $pk = [
        'idCountry'
    ];

    protected array $columns = [
        'idCountry',
        'isoCode',
        'countryName',
        'locale',
        'capital',
        'continent',
        'tld',
        'currencyCode',
        'currencyName',
        'phonePrefix',
        'postalCodeFormat',
        'postalCodeRegex',
        'idLanguage',
        'languages',
        'blockUpdate',
        'supported'
    ];

    public function exists(string $iso3316_alpha2): bool
    {
        $filter = [
            ['isoCode', 'equal', $iso3316_alpha2]
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}