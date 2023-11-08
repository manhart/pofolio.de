<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * SearchTicker.php created on 08.11.23, 19:55.
 */

namespace pofolio\classes\FMP\Response;

class SearchTicker extends Search
{
    protected static string $url = 'v3/search-ticker';
}