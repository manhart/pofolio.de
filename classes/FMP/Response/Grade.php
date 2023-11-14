<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Grade.php created on 14.11.23, 23:54.
 */

namespace pofolio\classes\FMP\Response;

class Grade extends Response
{
    protected static string $url = 'v3/grade';

    public function getSymbol(): string
    {
        return $this->getResponseValue('symbol');
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->getResponseValueAsDate('date');
    }

    public function getGradingCompany(): string
    {
        return $this->getResponseValueAsString('gradingCompany');
    }

    public function getPreviousGrade(): string
    {
        return $this->getResponseValueAsString('previousGrade');
    }

    public function getNewGrade(): string
    {
        return $this->getResponseValueAsString('newGrade');
    }
}