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

class Stock extends PofolioDAO
{
    protected static ?string $tableName = 'Stock';

    protected array $pk = [
        'idStock'
    ];

    protected array $columns = [
        'idStock',
        'symbol',
        'type',
        'name',
        'idExchange',
        'tradeable',
    ];

    protected array $metaData = [
        'columns' => [
            'idStock' => [
                'type' => 'int',
                'phpType' => 'int',
                'length' => 11,
                'auto_increment' => true,
                'nullable' => false,
                'unsigned' => true,
            ],
            'symbol' => [
                'type' => 'varchar',
                'phpType' => 'string',
                'length' => 13,
                'nullable' => false,
            ],
            'type' => [
                'type' => 'enum',
                'phpType' => 'string',
                'nullable' => false,
            ],
            'name' => [
                'type' => 'varchar',
                'phpType' => 'string',
                'length' => 255,
                'nullable' => false,
            ],
            'price' => [
                'type' => 'decimal',
                'phpType' => 'float',
                'length' => 10,
                'precision' => 2,
                'nullable' => false,
                'unsigned' => true,
            ],
            'volume' => [
                'type' => 'int',
                'phpType' => 'int',
                'length' => 11,
                'nullable' => false,
                'unsigned' => true,
            ],
            'idExchange' => [
                'type' => 'mediumint',
                'phpType' => 'int',
                'length' => 9,
                'nullable' => false,
            ],
            'tradeable' => [
                'type' => 'tinyint',
                'phpType' => 'bool',
                'length' => 1,
                'nullable' => false,
            ],
            'idCurrency' => [
                'type' => 'mediumint',
                'phpType' => 'int',
                'length' => 9,
                'nullable' => false,
            ],
            'idIndustry' => [
                'type' => 'mediumint',
                'phpType' => 'int',
                'length' => 9,
                'nullable' => false,
            ],
            'idSector' => [
                'type' => 'mediumint',
                'phpType' => 'int',
                'length' => 9,
                'nullable' => false,
            ],
        ]
    ];

    public function exists(string $symbol): bool
    {
        $filter = [
            ['symbol', 'equal', $symbol]
        ];
        return $this->getCount(filter: $filter)->getValueAsBool('count');
    }
}