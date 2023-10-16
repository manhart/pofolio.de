<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * DelistedCompanies.php created on 27.09.23, 20:32.
 */

namespace pofolio\classes\FMP\Response;

class DelistedCompanies extends Response
{
    protected static string $url = 'v3/delisted-companies';

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getCompanyName(): string
    {
        return $this->getResponseValueAsString('companyName');
    }

    public function getExchange(): string
    {
        return $this->getResponseValueAsString('exchange');
    }

    public function getIpoDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('ipoDate');
    }

    public function getDelistedDate(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('delistedDate');
    }
}