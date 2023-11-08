<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CIKList.php created on 08.11.23, 20:57.
 */

namespace pofolio\classes\FMP\Response;

class CIKList extends CIK
{
    protected static string $url = 'v3/cik_list';
}