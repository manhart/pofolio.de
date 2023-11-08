<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * EtfList.php created on 05.11.23, 20:50.
 */

namespace pofolio\classes\FMP\Response;

class EtfList extends StockList
{
    protected static string $url = 'v3/etf/list';
}