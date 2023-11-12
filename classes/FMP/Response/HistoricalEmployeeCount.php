<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * HistoricalEmployeeCount.php created on 12.11.23, 17:54.
 */

namespace pofolio\classes\FMP\Response;

class HistoricalEmployeeCount extends EmployeeCount
{
    protected static string $url = 'v4/historical/employee_count';
}