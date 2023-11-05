<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * FmpApiClient.php created on 25.09.23, 22:48.
 */

namespace pofolio\classes\FMP\Client;

use pofolio\classes\FMP\Response\Quote;
use pofolio\classes\FMP\Response\QuoteShort;
use pofolio\classes\FMP\Response\BalanceSheetStatement;
use pofolio\classes\FMP\Response\CashflowStatement;
use pofolio\classes\FMP\Response\CompanyCoreInformation;
use pofolio\classes\FMP\Response\DelistedCompanies;
use pofolio\classes\FMP\Response\HistoricalPrice;
use pofolio\classes\FMP\Response\IncomeStatement;
use pofolio\classes\FMP\Response\PriceTarget;
use pofolio\classes\FMP\Response\Profile;
use pofolio\classes\FMP\Response\ShareFloat;
use pofolio\classes\FMP\Response\StockDividend;
use pofolio\classes\FMP\Response\StockDividendCalendar;
use pofolio\classes\FMP\Response\StockList;
use pofolio\classes\FMP\Response\StockSplit;
use pofolio\classes\FMP\Response\StockSplitCalendar;
use pofolio\classes\FMP\Response\UpgradesDowngrades;
use pool\classes\Exception\CurlException;
use function curl_close;
use function curl_error;
use function curl_exec;
use function curl_setopt;

class FmpApiClient
{
    /**
     * @var FmpApiClient|null Instance of the FMPServiceProvider
     */
    private static ?FmpApiClient $instance = null;

    /**
     * @var string Base URL of the API
     */
    protected string $baseUrl = 'https://financialmodelingprep.com/api';

    /**
     * @var string API Key for accessing the API
     */
    private readonly string $apiKey;

    /**
     * @var int Amount of Requests
     */
    private int $requests = 0;

    /**
     * @var string Last endpoint URL called by API
     */
    private string $lastEndpointURL = '';

    /**
     * FMPServiceProvider constructor (private to prevent instantiation)
     */
    final private function __construct()
    {
        $this->apiKey = \getenv('FMP_API_KEY');
    }

    public function getFmpApiClient(): FmpApiClient
    {
        return self::getInstance();
    }

    public static function getInstance(): FmpApiClient
    {
        if(self::$instance === null) {
            self::$instance = new FmpApiClient();
        }

        return self::$instance;
    }

    /**
     * @throws CurlException
     */
    public function executeCurl(string $endpoint, $params = []): string
    {
        $url = $this->buildEndpointURL($endpoint, $params);

        \set_time_limit(0);

        $channel = \curl_init();

        curl_setopt($channel, \CURLOPT_URL, $url);
        curl_setopt($channel, \CURLOPT_AUTOREFERER, true);
        curl_setopt($channel, \CURLOPT_HEADER, 0);
        curl_setopt($channel, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, \CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($channel, \CURLOPT_IPRESOLVE, \CURL_IPRESOLVE_V4);
        curl_setopt($channel, \CURLOPT_TIMEOUT, 0);
        curl_setopt($channel, \CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($channel, \CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($channel, \CURLOPT_SSL_VERIFYPEER, 1);

        $output = curl_exec($channel);

        if($error = curl_error($channel)) {
            curl_close($channel);
            throw new CurlException($error, \curl_errno($channel));
        }
        curl_close($channel);
        $this->requests++;

        return $output;
    }

    /**
     * Builds the endpoint URL for API requests.
     *
     * @param string $endpoint The API endpoint.
     * @param mixed $params The parameters to include in the URL.
     *
     * @return string The built endpoint URL.
     */
    private function buildEndpointURL(string $endpoint, mixed $params): string
    {
        while($params && !\is_string(\array_key_first($params))) {
            $param = \array_shift($params);
            $endpoint .= '/'.(\is_array($param) ? \implode(',', $param) : $param);
        }
        $params['apikey'] = $this->apiKey;
        $query = \http_build_query($params);
        return $this->lastEndpointURL = "$this->baseUrl/$endpoint?$query";
    }

    /**
     * @return int Amount of Requests
     */
    public function getRequests(): int
    {
        return $this->requests;
    }

    public function getProfile(string $symbol): Profile
    {
        return Profile::create($this, $symbol);
    }

    public function getStockList(): StockList
    {
        return StockList::create($this);
    }

    public function getShareFloat(?string $symbol = null): ShareFloat
    {
        return ($symbol) ? ShareFloat::create($this, symbol: $symbol) : ShareFloat::create($this, 'all');
    }

    public function getStockDividend(string $symbol): StockDividend
    {
        return StockDividend::create($this, $symbol);
    }

    public function getStockDividendCalendar(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): StockDividendCalendar
    {
        $from_string = $from?->format('Y-m-d');
        $to_string = $to?->format('Y-m-d');
        return StockDividendCalendar::create($this, from: $from_string, to: $to_string);
    }

    public function getStockSplit(string $symbol): StockSplit
    {
        return StockSplit::create($this, $symbol);
    }

    public function getStockSplitCalendar(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): StockSplitCalendar
    {
        $from_string = $from?->format('Y-m-d');
        $to_string = $to?->format('Y-m-d');
        return StockSplitCalendar::create($this, from: $from_string, to: $to_string);
    }

    public function getPriceTarget(string $symbol): PriceTarget
    {
        return PriceTarget::create($this, symbol: $symbol);
    }

    public function getUpgradesDowngrades(string $symbol): UpgradesDowngrades
    {
        return UpgradesDowngrades::create($this, symbol: $symbol);
    }

    public function getHistoricalPrice(string $symbol, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null, ?int $timeseries = null): HistoricalPrice
    {
        $from_string = $from?->format('Y-m-d');
        $to_string = $to?->format('Y-m-d');
        return HistoricalPrice::create($this, $symbol, from: $from_string, to: $to_string, timeseries: $timeseries);
    }

    public function getCompanyCoreInformation(string $symbol): CompanyCoreInformation
    {
        return CompanyCoreInformation::create($this, symbol: $symbol);
    }

    public function getDelistedCompanies(): DelistedCompanies
    {
        return DelistedCompanies::create($this);
    }

    public function getIncomeStatement(string $symbol, ?string $period = IncomeStatement::PERIOD_ANNUAL, ?string $dataType = 'json', ?int $limit = null): IncomeStatement
    {
        return IncomeStatement::create($this, $symbol, limit: $limit, period: $period);
    }

    public function getBalanceSheetStatement(string|int $symbolOrCIK, ?string $period = IncomeStatement::PERIOD_ANNUAL, ?string $dataType = 'json', ?int $limit = null): BalanceSheetStatement
    {
        if(is_int($symbolOrCIK)) {
            // format CIK as string
            $symbolOrCIK = \str_pad((string)$symbolOrCIK, 10, '0', \STR_PAD_LEFT);
        }
        return BalanceSheetStatement::create($this, $symbolOrCIK, limit: $limit, period: $period);
    }

    public function getCashflowStatement(string|int $symbolOrCIK, ?string $period = IncomeStatement::PERIOD_ANNUAL, ?string $dataType = 'json', ?int $limit = null): CashflowStatement
    {
        if(is_int($symbolOrCIK)) {
            // format CIK as string
            $symbolOrCIK = \str_pad((string)$symbolOrCIK, 10, '0', \STR_PAD_LEFT);
        }
        return CashflowStatement::create($this, $symbolOrCIK, limit: $limit, period: $period);
    }

    public function getQuote(string|array $symbol): Quote
    {
        return Quote::create($this, $symbol);
    }

    public function getQuoteShort(string $symbol): QuoteShort
    {
        return QuoteShort::create($this, $symbol);
    }

    public function getLastEndpointURL(): string
    {
        return $this->lastEndpointURL;
    }
}