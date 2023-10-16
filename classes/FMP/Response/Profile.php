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

class Profile extends Response
{
    protected static string $url = 'v3/profile';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getPrice(): ?float
    {
        return $this->getResponseValueAsFloat('price');
    }

    public function getBeta(): ?float
    {
        return $this->getResponseValueAsFloat('beta');
    }

    public function getLastDiv(): ?float
    {
        return $this->getResponseValueAsFloat('lastDiv');
    }

    public function getRange(): ?string
    {
        return $this->getResponseValue('range');
    }

    public function getChanges(): ?float
    {
        return $this->getResponseValueAsFloat('changes');
    }

    public function getCompanyName(): string
    {
        return $this->getResponseValue('companyName');
    }

    public function getWebsite(): ?string
    {
        return $this->getResponseValue('website');
    }

    public function getDescription(): ?string
    {
        return $this->getResponseValue('description');
    }

    public function getCountry(): ?string
    {
        return $this->getResponseValue('country');
    }

    public function getFullTimeEmployees(): ?string
    {
        return $this->getResponseValue('fullTimeEmployees');
    }

    public function getPhone(): ?string
    {
        return $this->getResponseValue('phone');
    }

    public function getAddress(): ?string
    {
        return $this->getResponseValue('address');
    }

    public function getCity(): ?string
    {
        return $this->getResponseValue('city');
    }

    public function getZip(): ?string
    {
        return $this->getResponseValue('zip');
    }

    public function getState(): ?string
    {
        return $this->getResponseValue('state');
    }

    public function getVolAvg(): ?int
    {
        return $this->getResponseValueAsInt('volAvg') ?: null;
    }

    public function getExchange(): ?string
    {
        return $this->getResponseValue('exchange');
    }

    public function getExchangeShortName(): ?string
    {
        return $this->getResponseValue('exchangeShortName');
    }

    public function getIndustry(): ?string
    {
        return $this->getResponseValue('industry');
    }

    /**
     * @return int|null Central Index Key
     */
    public function getCIK(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getISIN(): ?string
    {
        return $this->getResponseValue('isin');
    }

    /**
     * @return string|null Committee on Uniform Securities Identification Procedures
     */
    public function getCUSIP(): ?string
    {
        return $this->getResponseValue('cusip');
    }

    public function getSector(): ?string
    {
        return $this->getResponseValue('sector');
    }

    public function getMktCap(): ?int
    {
        return $this->getResponseValueAsInt('mktCap') ?: null;
    }

    public function getCurrency(): ?string
    {
        return $this->getResponseValue('currency');
    }

    public function getCEO(): ?string
    {
        return $this->getResponseValue('ceo');
    }

    public function getDcfDiff(): ?float
    {
        return $this->getResponseValueAsFloat('dcfDiff');
    }

    public function getDcf(): ?float
    {
        return $this->getResponseValueAsFloat('dcf') ?: null;
    }

    public function getImage(): ?string
    {
        return $this->getResponseValue('image');
    }

    public function getDefaultImage(): bool
    {
        return $this->getResponseValue('defaultImage');
    }

    public function getIpoDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('ipoDate');
    }

    public function isEtf(): bool
    {
        return $this->getResponseValueAsBool('isEtf');
    }

    public function isActivelyTrading(): bool
    {
        return $this->getResponseValueAsBool('isActivelyTrading');
    }

    public function isAdr(): bool
    {
        return $this->getResponseValueAsBool('isAdr');
    }

    public function isFund(): bool
    {
        return $this->getResponseValueAsBool('isFund');
    }

    public function ipoDate(): ?string
    {
        return $this->getResponseValue('ipoDate');
    }

    public function getType(): string
    {
        if($this->isEtf()) {
            return 'etf';
        }

        if($this->isFund()) {
            return 'trust';
        }

        return 'stock';
    }
}