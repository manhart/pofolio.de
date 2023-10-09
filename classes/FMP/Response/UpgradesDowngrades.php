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

class UpgradesDowngrades extends Response
{
    protected static string $url = 'v4/upgrades-downgrades';

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('publishedDate');
    }

    public function getNewGrade(): ?string
    {
        return $this->getResponseValue('newGrade');
    }

    public function getPreviousGrade(): ?string
    {
        return $this->getResponseValue('previousGrade');
    }

    public function getGradingCompany(): ?string
    {
        return $this->getResponseValue('gradingCompany');
    }

    public function getAction(): ?string
    {
        return $this->getResponseValue('action');
    }

    public function getPriceWhenPosted(): float
    {
        return $this->getResponseValueAsFloat('priceWhenPosted');
    }

    public function getNewsBaseURL(): ?string
    {
        return $this->getResponseValue('newsBaseURL');
    }

    public function getNewsPublisher(): ?string
    {
        return $this->getResponseValue('newsPublisher');
    }

    public function getNewsURL(): ?string
    {
        return $this->getResponseValue('newsURL');
    }

    public function getNewsTitle(): string
    {
        return $this->getResponseValueAsString('newsTitle');
    }
}