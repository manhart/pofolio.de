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

class CIK extends Response
{
    protected static string $url = 'v3/cik';

    public function getName(): string
    {
        return $this->getResponseValueAsString('name');
    }

    /**
     * @return int|null Central Index Key
     */
    public function getCIKAsInt(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getCIK(): string
    {
        return $this->getResponseValueAsString('cik');
    }
}