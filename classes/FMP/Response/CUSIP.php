<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CIK.php created on 08.11.23, 20:20.
 */

namespace pofolio\classes\FMP\Response;

class CUSIP extends Response
{
    protected static string $url = 'v3/cusip';

    public function getCompany(): string
    {
        return $this->getResponseValueAsString('company');
    }

    public function getTicker(): string
    {
        return $this->getResponseValueAsString('ticker');
    }

    public function getCUSIP(): string
    {
        return $this->getResponseValueAsString('cusip');
    }

}