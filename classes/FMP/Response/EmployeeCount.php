<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * HistoricalEmployeeCount.php created on 12.11.23, 17:54.
 */

namespace pofolio\classes\FMP\Response;

class EmployeeCount extends Response
{
    protected static string $url = 'v4/historical/employee_count';

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

    public function getAcceptanceTime(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('acceptanceTime');
    }

    public function getPeriodOfReport(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('periodOfReport');
    }

    public function getCompanyName(): string
    {
        return $this->getResponseValueAsString('companyName');
    }

    public function getFormType(): string
    {
        return $this->getResponseValueAsString('formType');
    }

    public function getFillingDate(): \DateTimeInterface
    {
        return $this->getResponseValueAsDate('fillingDate');
    }

    public function getEmployeeCount(): int
    {
        return $this->getResponseValueAsInt('employeeCount');
    }

    public function getSource(): string
    {
        return $this->getResponseValueAsString('source');
    }
}