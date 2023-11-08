<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CommitmentOfTradersReportList.php created on 08.11.23, 20:52.
 */

namespace pofolio\classes\FMP\Response;

class CommitmentOfTradersReportList extends Response
{
    protected static string $url = 'v4/commitment_of_traders_report/list';

    public function getTradingSymbol(): string
    {
        return $this->getResponseValueAsString('trading_symbol');
    }

    public function getShortName(): string
    {
        return $this->getResponseValueAsString('short_name');
    }
}