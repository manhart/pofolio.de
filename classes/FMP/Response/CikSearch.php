<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Search.php created on 08.11.23, 19:31.
 */

namespace pofolio\classes\FMP\Response;

class CikSearch extends Response
{
    protected static string $url = 'v3/cik-search';

    public function getName(): string
    {
        return $this->getResponseValueAsString('name');
    }

    /**
     * @return int|null Central Index Key
     */
    public function getCIK(): ?int
    {
        return $this->getResponseValueAsInt('cik') ?: null;
    }

    public function getCIKAsString(): string
    {
        return $this->getResponseValueAsString('cik');
    }
}