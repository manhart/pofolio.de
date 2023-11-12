<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * CompanyNotes.php created on 12.11.23, 17:46.
 */

namespace pofolio\classes\FMP\Response;

class CompanyNotes extends Response
{
    protected static string $url = 'v4/company-notes';

    public function getCIK(): string
    {
        return $this->getResponseValueAsString('cik');
    }

    public function getCIKAsInt(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getSymbol(): string
    {
        return $this->getResponseValueAsString('symbol');
    }

    public function getTitle(): string
    {
        return $this->getResponseValueAsString('title');
    }

    public function getExchange(): string
    {
        return $this->getResponseValueAsString('exchange');
    }
}