<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Factory.php created on 25.09.23, 22:53.
 */

namespace pofolio\classes\FMP\Factory;

use pofolio\classes\FMP\Client\FmpApiClient;
use pofolio\classes\FMP\Response\ResponseInterface;

interface FactoryInterface
{
    public static function create(FmpApiClient $client, ...$params): ResponseInterface;
}