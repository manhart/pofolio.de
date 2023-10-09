<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Response.php created on 27.09.23, 20:28.
 */

namespace pofolio\classes\FMP\Response;

interface ResponseInterface
{
    public function __construct(array $response);
    public static function getUrl(): string;
    public function getResponseValue(string $key): mixed;
}