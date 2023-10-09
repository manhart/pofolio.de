<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Pofolio_DAO.php created on 26.09.23, 00:15.
 */

namespace pofolio\classes;

use pool\classes\Database\DAO\MySQL_DAO;

class PofolioDAO extends MySQL_DAO
{
    protected static ?string $databaseName = 'pofolio';
}