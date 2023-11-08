<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * AvailableTradedList.php created on 08.11.23, 20:42.
 */

namespace pofolio\classes\FMP\Response;

class AvailableTradedList extends StockList
{
    protected static string $url = 'v3/available-traded/list';
}