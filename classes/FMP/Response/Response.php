<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Response.php created on 27.09.23, 21:09.
 */

namespace pofolio\classes\FMP\Response;

use Iterator;
use JsonException;
use pofolio\classes\FMP\Client\FmpApiClient;
use pofolio\classes\FMP\Exception\ResponseException;
use pofolio\classes\FMP\Factory\FactoryInterface;
use Throwable;
use function json_decode;

class Response implements ResponseInterface, FactoryInterface, Iterator, \Countable
{
    private const JSON_DECODE_OPTIONS = 512;

    protected static string $url;

    protected static FmpApiClient $client;

    protected array $response;

    private int $position = 0;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * @throws ResponseException
     */
    public static function create(FmpApiClient $client, ...$params): static
    {
        try {
            $json = $client->executeCurl(static::getUrl(), $params);
            $data = static::decodeJson($json);
        }
        catch(Throwable $e) {
            // Possible logging of the error
            throw new ResponseException('Failed to create response', previous: $e);
        }

        // handle known API errors from FMP
        if(isset($data['status'], $data['error'])) {
            if($data['status'] === 404) {
                throw new ResponseException('Path '.$client->getLastEndpointURL().' not found.');
            }

            throw new ResponseException("{$data['error']}:{$data['message']} ({$data['status']})");
        }

        return new static($data);
    }

    public static function getUrl(): string
    {
        return static::$url;
    }

    /**
     * @throws JsonException
     */
    protected static function decodeJson(string $json): array
    {
        return json_decode($json, true, self::JSON_DECODE_OPTIONS, \JSON_THROW_ON_ERROR);
    }

    /**
     * Move forward to next element
     *
     * @link https://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     *
     * @link https://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link https://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    public function getResponseValueAsInt(string $key, mixed $default = 0): int
    {
        return ($value = $this->getResponseValue($key, $default)) === null ? $default : (int)$value;
    }

    public function getResponseValueAsString(string $key, mixed $default = ''): string
    {
        return ($value = $this->getResponseValue($key, $default)) === null ? $default : (string)$value;
    }

    public function getResponseValueAsFloat(string $key, mixed $default = 0.0): float
    {
        return ($value = $this->getResponseValue($key, $default)) === null ? $default : (float)$value;
    }

    public function getResponseValueAsDate(string $key, mixed $default = null): ?\DateTimeInterface
    {
        try {
            $value = $this->getResponseValue($key, $default);
            // remove line breaks, because they are not allowed in a DateTime string (ISO 8601)
            $value = mb_ereg_replace("(\r\n|\r|\n)", '', $value);
            $value = empty($value) ? $default : new \DateTime($value);
        }
        catch(\Exception) {
            $value = $default;
        }
        return $value;
    }

    public function getResponseValueAsBool(string $key, mixed $default = false): bool
    {
        return ($value = $this->getResponseValue($key, $default)) === null ? $default : (bool)$value;
    }

    public function getResponseValue(string $key, mixed $default = null): mixed
    {
        return $this->valid() ? ($this->current()[$key] ?? $default) : $default;
    }

    /**
     * Checks if current position is valid
     *
     * @link https://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return isset($this->response[$this->position]);
    }

    /**
     * Return the current element
     *
     * @link https://php.net/manual/en/iterator.current.php
     */
    public function current(): array
    {
        return $this->response[$this->position];
    }

    public function dump(): void
    {
        /** @noinspection ForgottenDebugOutputInspection */
        \var_dump($this->response);
    }

    public function count(): int
    {
        return \count($this->response);
    }

    /**
     * @return bool response data available
     */
    public function hasResponse(): bool
    {
        return (bool)$this->response;
    }
}