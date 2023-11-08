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

class Language extends PofolioDAO
{
    protected static ?string $tableName = 'Language';

    protected array $pk = [
        'idLanguage'
    ];

    protected array $columns = [
        'idLanguage',
        'code',
        'language',
        'locale',
        'supported'
    ];

    public function exists(string $code): bool
    {
        $filter = [
            ['code', 'equal', $code]
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}