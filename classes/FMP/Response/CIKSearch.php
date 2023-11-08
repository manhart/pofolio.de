<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CIKSearch.php created on 08.11.23, 20:20.
 */

namespace pofolio\classes\FMP\Response;

class CIKSearch extends CIK
{
    protected static string $url = 'v3/cik-search';
}