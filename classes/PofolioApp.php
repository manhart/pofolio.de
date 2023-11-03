<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Pofolio.php created on 17.09.23, 12:49.
 */

namespace pofolio\classes;

use pool\classes\Core\Weblication;
use pool\classes\Database\DataInterface;

class PofolioApp extends Weblication
{
    public function setup(array $settings = []): static
    {
        parent::setup($settings);
        $connectOptions = [
            'host' => MYSQL_HOST,
            'database' => constant('DB_POFOLIO'),
        ];
        $this->addDataInterface(DataInterface::createDataInterface($connectOptions));
        return $this;
    }
}