<?php
return [
    'mysql_auth' => [
        'localhost' => [
            DB_POFOLIO => json_decode(getenv('MARIADB_AUTH'), true, 512, JSON_THROW_ON_ERROR)
        ]
    ]
];