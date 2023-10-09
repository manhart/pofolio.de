<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Language.php created on 09.10.23, 22:57.
 */

namespace pofolio\dao\mysql\pofolio;

use pofolio\classes\PofolioDAO;

class Locale extends PofolioDAO
{
    protected static ?string $tableName = 'Locale';

    protected array $pk = [
        'idLocale'
    ];

    protected array $columns = [
        'idLocale',
        'locale',
        'idLanguage',
        'idCountry'
    ];

    public function exists(string $locale): bool
    {
        $filter = [
            ['locale', 'equal', $locale]
        ];
        return $this->getCount(filter_rules: $filter)->getValueAsBool('count');
    }
}