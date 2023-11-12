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

use pofolio\classes\FMP\Exception\ResponseException;
use pofolio\classes\FMP\Response\AvailableEuronext;
use pofolio\classes\FMP\Response\AvailableTradedList;
use pofolio\classes\FMP\Response\BalanceSheetStatement;
use pofolio\classes\FMP\Response\CashflowStatement;
use pofolio\classes\FMP\Response\CIK;
use pofolio\classes\FMP\Response\CIKList;
use pofolio\classes\FMP\Response\CIKSearch;
use pofolio\classes\FMP\Response\CommitmentOfTradersReportList;
use pofolio\classes\FMP\Response\CompanyCoreInformation;
use pofolio\classes\FMP\Response\CompanyNotes;
use pofolio\classes\FMP\Response\CUSIP;
use pofolio\classes\FMP\Response\DelistedCompanies;
use pofolio\classes\FMP\Response\EmployeeCount;
use pofolio\classes\FMP\Response\EtfList;
use pofolio\classes\FMP\Response\ExchangeSymbols;
use pofolio\classes\FMP\Response\ExecutiveCompensation;
use pofolio\classes\FMP\Response\ExecutiveCompensationBenchmark;
use pofolio\classes\FMP\Response\FinancialStatementSymbolLists;
use pofolio\classes\FMP\Response\HistoricalPrice;
use pofolio\classes\FMP\Response\IncomeStatement;
use pofolio\classes\FMP\Response\PriceTarget;
use pofolio\classes\FMP\Response\Profile;
use pofolio\classes\FMP\Response\Quote;
use pofolio\classes\FMP\Response\QuoteShort;
use pofolio\classes\FMP\Response\Search;
use pofolio\classes\FMP\Response\SearchName;
use pofolio\classes\FMP\Response\SearchTicker;
use pofolio\classes\FMP\Response\ShareFloat;
use pofolio\classes\FMP\Response\StockDividend;
use pofolio\classes\FMP\Response\StockDividendCalendar;
use pofolio\classes\FMP\Response\StockList;
use pofolio\classes\FMP\Response\StockSplit;
use pofolio\classes\FMP\Response\StockSplitCalendar;
use pofolio\classes\FMP\Response\SymbolChange;
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
            $endpoint .= '/' . (\is_array($param) ? \implode(',', $param) : $param);
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

    /**
     * Search over 70,000 symbols by symbol name or company name, including cryptocurrencies, forex, stocks, etf and other financial instruments.
     *
     * @param string $query The query to search for.
     * @param int|null $limit The maximum amount of results to return.
     * @param string|null $exchange The short exchange name to search in.
     *
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#general-search-company-search
     */
    public function search(string $query, int $limit = null, string $exchange = null): Search
    {
        return Search::create($this, query: $query, limit: $limit, exchange: $exchange);
    }

    /**
     * Find ticker symbols and exchanges for both equity securities and exchange-traded funds (ETFs) by searching with the company name or ticker symbol.
     * @link https://site.financialmodelingprep.com/developer/docs#ticker-search-company-search
     * @throws ResponseException
     */
    public function searchTicker(string $query, int $limit = null, string $exchange = null): SearchTicker
    {
        return SearchTicker::create($this, query: $query, limit: $limit, exchange: $exchange);
    }

    /**
     * Find ticker symbols and exchange information for equity securities and exchange-traded funds (ETFs) by searching with the company name.
     * @link https://site.financialmodelingprep.com/developer/docs#name-search-company-search
     * @throws ResponseException
     */
    public function searchName(string $query, int $limit = null, string $exchange = null): SearchName
    {
        return SearchName::create($this, query: $query, limit: $limit, exchange: $exchange);
    }

    /**
     * Discover CIK numbers for SEC-registered entities with our CIK Name Search.
     *
     * @param string $companyName The name of the company to search for.
     *
     * @return CIKSearch The CIKSearch object for the specified company name.
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#cik-name-search-company-search
     */
    public function searchCIKByName(string $companyName): CIKSearch
    {
        return CIKSearch::create($this, $companyName);
    }

    /**
     * Quickly find registered company names linked to SEC-registered entities using their CIK Number with our CIK Search
     *
     * @param string|int $CIK The Central Index Number to search for.
     *
     * @return CIK
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#cik-search-company-search
     */
    public function getCIK(string|int $CIK): CIK
    {
        if(is_int($CIK)) {
            // format CIK as string
            $CIK = \str_pad((string)$CIK, 10, '0', \STR_PAD_LEFT);
        }
        return CIK::create($this, $CIK);
    }

    /**
     * Access information about financial instruments and securities by simply entering their unique CUSIP (Committee on Uniform Securities Identification
     * Procedures) numbers with our CUSIP Search.
     *
     * @param string $CUSIP The CUSIP to search for.
     *
     * @return CUSIP
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#cusip-search-company-search
     */
    public function getCUSIP(string $CUSIP): CUSIP
    {
        return CUSIP::create($this, $CUSIP);
    }

    public function getStockList(): StockList
    {
        return StockList::create($this);
    }

    /**
     * Our Exchange Traded Fund Search makes it easy to find the symbol for any ETF you're looking for. Simply enter the ETF's name and we'll
     * return the symbol, name, and price.
     *
     * @return EtfList
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#exchange-traded-fund-search-stock-list
     */
    public function getEtfList(): EtfList
    {
        return EtfList::create($this);
    }

    /**
     * Discover all companies with financial statements available on our API. Our comprehensive list covers major exchanges such as the NYSE and NASDAQ,
     * as well as international exchanges. This list is regularly updated, so you can always find the information you need.
     *
     * @return FinancialStatementSymbolLists
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#statement-symbols-list-stock-list
     */
    public function getFinancialStatementSymbolLists(): FinancialStatementSymbolLists
    {
        return FinancialStatementSymbolLists::create($this);
    }

    /**
     * Discover all actively traded stocks with our Tradable Search feature. This comprehensive list includes over 70,000 stocks, with symbol, name, price, and exchange information for each company.
     *
     * @return AvailableTradedList
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#tradable-search-stock-list
     */
    public function getAvailableTradedList(): AvailableTradedList
    {
        return AvailableTradedList::create($this);
    }

    /**
     * The Commitment of Traders Report is a weekly report from the Commodity Futures Trading Commission (CFTC) that provides insights into the
     * positions of market participants in various markets. Our Commitment of Traders Report tool makes it easy to access and analyze this valuable
     * data, helping you to make more informed trading decisions.
     *
     * @return CommitmentOfTradersReportList
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#commitment-of-traders-report-stock-list
     */
    public function getCommitmentOfTradersReportList(): CommitmentOfTradersReportList
    {
        return CommitmentOfTradersReportList::create($this);
    }

    public function getCIKList(): CIKList
    {
        return CIKList::create($this);
    }

    /**
     * Find all symbols for stocks traded on Euronext exchanges with our comprehensive list. Euronext is one of the largest exchanges in Europe, and our
     * list includes stocks from a wide range of industries.
     *
     * @return AvailableEuronext
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#euronext-symbols-stock-list
     */
    public function getAvailableEuronextSymbols(): AvailableEuronext
    {
        return AvailableEuronext::create($this);
    }

    /**
     * Stay up-to-date on the latest symbol changes with our easy-to-use tool. Track symbol changes due to mergers, acquisitions, stock splits, and name changes.
     *
     * @return SymbolChange
     * @throws ResponseException
     * @link https://site.financialmodelingprep.com/developer/docs#symbol-changes-stock-list
     */
    public function getSymbolChanges(): SymbolChange
    {
        return SymbolChange::create($this);
    }

    public function getExchangeSymbols(string $exchange): ExchangeSymbols
    {
        return ExchangeSymbols::create($this, $exchange);
    }

    public function getProfile(string $symbol): Profile
    {
        return Profile::create($this, $symbol);
    }

    public function getExecutiveCompensation(string $symbol): ExecutiveCompensation
    {
        return ExecutiveCompensation::create($this, symbol: $symbol);
    }

    public function getExecutiveCompensationBenchmark(int $year): ExecutiveCompensationBenchmark
    {
        return ExecutiveCompensationBenchmark::create($this, year: $year);
    }

    public function getCompanyNotes(string $symbol): CompanyNotes
    {
        return CompanyNotes::create($this, symbol: $symbol);
    }

    public function getEmployeeCount(string $symbol): EmployeeCount
    {
        return EmployeeCount::create($this, symbol: $symbol);
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

    public function getHistoricalPrice(
        string $symbol,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null,
        ?int $timeseries = null
    ): HistoricalPrice {
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

    public function getIncomeStatement(
        string $symbol,
        ?string $period = IncomeStatement::PERIOD_ANNUAL,
        ?string $dataType = 'json',
        ?int $limit = null
    ): IncomeStatement {
        return IncomeStatement::create($this, $symbol, limit: $limit, period: $period);
    }

    public function getBalanceSheetStatement(
        string|int $symbolOrCIK,
        ?string $period = IncomeStatement::PERIOD_ANNUAL,
        ?string $dataType = 'json',
        ?int $limit = null
    ): BalanceSheetStatement {
        if(is_int($symbolOrCIK)) {
            // format CIK as string
            $symbolOrCIK = \str_pad((string)$symbolOrCIK, 10, '0', \STR_PAD_LEFT);
        }
        return BalanceSheetStatement::create($this, $symbolOrCIK, limit: $limit, period: $period);
    }

    public function getCashflowStatement(
        string|int $symbolOrCIK,
        ?string $period = IncomeStatement::PERIOD_ANNUAL,
        ?string $dataType = 'json',
        ?int $limit = null
    ): CashflowStatement {
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