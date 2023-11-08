<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * AvailableEuronext.php created on 08.11.23, 21:03.
 */

namespace pofolio\classes\FMP\Response;

class AvailableEuronext extends Search
{
    protected static string $url = 'v3/symbol/available-euronext';
}