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

class PriceTarget extends Response
{
    protected static string $url = 'v4/price-target';

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('publishedDate');
    }

    public function getNewsURL(): ?string
    {
        return $this->getResponseValue('newsURL');
    }

    public function getNewsTitle(): string
    {
        return $this->getResponseValueAsString('newsTitle');
    }

    public function getAnalystName(): ?string
    {
        return $this->getResponseValue('analystName');
    }

    public function getPriceTarget(): float
    {
        return $this->getResponseValueAsFloat('priceTarget');
    }

    public function getAdjPriceTarget(): float
    {
        return $this->getResponseValueAsFloat('adjPriceTarget');
    }

    public function getPriceWhenPosted(): float
    {
        return $this->getResponseValueAsFloat('priceWhenPosted');
    }

    public function getNewsPublisher(): ?string
    {
        return $this->getResponseValue('newsPublisher');
    }

    public function getNewsBaseURL(): ?string
    {
        return $this->getResponseValue('newsBaseURL');
    }

    public function getAnalystCompany(): ?string
    {
        return $this->getResponseValue('analystCompany');
    }
}