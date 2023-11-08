<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Profile.php created on 27.09.23, 20:32.
 */

namespace pofolio\classes\FMP\Response;

class CompanyCoreInformation extends Response
{
    protected static string $url = 'v4/company-core-information';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getCIKAsInt(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getCIK(): ?string
    {
        return $this->getResponseValue('cik') ?: null;
    }

    public function getExchange(): string
    {
        return $this->getResponseValueAsString('exchange');
    }

    public function getSICCode(): ?int
    {
        return $this->getResponseValueAsInt('sicCode') ?: null;
    }

    public function getSICGroup(): ?string
    {
        return $this->getResponseValue('sicGroup');
    }

    public function getSICDescription(): ?string
    {
        return $this->getResponseValue('sicDescription');
    }

    public function getStateLocation(): ?string
    {
        return $this->getResponseValue('stateLocation');
    }

    public function getStateOfIncorporation(): ?string
    {
        return $this->getResponseValue('stateOfIncorporation');
    }

    public function getFiscalYearEnd(): ?string
    {
        return $this->getResponseValue('fiscalYearEnd');
    }

    public function getBusinessAddress(): ?string
    {
        return $this->getResponseValue('businessAddress');
    }

    public function getMailingAddress(): ?string
    {
        return $this->getResponseValue('mailingAddress');
    }

    public function getTaxIdentificationNumber(): ?string
    {
        return $this->getResponseValue('taxIdentificationNumber');
    }

    public function getRegistrantName(): ?string
    {
        return $this->getResponseValue('registrantName');
    }
}