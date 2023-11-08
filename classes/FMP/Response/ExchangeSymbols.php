<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * getExchangeSymbols.php created on 08.11.23, 21:23.
 */

namespace pofolio\classes\FMP\Response;

class ExchangeSymbols extends Quote
{
    protected static string $url = 'v3/symbol';
}