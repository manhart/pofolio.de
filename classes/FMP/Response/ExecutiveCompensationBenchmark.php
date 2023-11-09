<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * ExecutiveCompensationBenchmark.php created on 09.11.23, 23:23.
 */

namespace pofolio\classes\FMP\Response;

class ExecutiveCompensationBenchmark extends Response
{
    protected static string $url = 'v4/executive-compensation-benchmark';

    public function getIndustryTitle(): string
    {
        return $this->getResponseValueAsString('industryTitle');
    }

    public function getYear(): int
    {
        return $this->getResponseValueAsInt('year');
    }


    public function getAverageCompensation(): float
    {
        return $this->getResponseValueAsFloat('averageCompensation');
    }
}