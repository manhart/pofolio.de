<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * stockList.php created on 24.09.23, 22:08.
 */

namespace pofolio\jobs;

// PHP gets an incredible amount of time
use pofolio\classes\FMP\Client\FmpApiClient;
use pofolio\dao\mysql\pofolio\Company;
use pofolio\dao\mysql\pofolio\Currency;
use pofolio\dao\mysql\pofolio\Dividend;
use pofolio\dao\mysql\pofolio\Exchange;
use pofolio\dao\mysql\pofolio\HistoricalPrice;
use pofolio\dao\mysql\pofolio\Industry;
use pofolio\dao\mysql\pofolio\PriceTarget;
use pofolio\dao\mysql\pofolio\Sector;
use pofolio\dao\mysql\pofolio\ShareFloat;
use pofolio\dao\mysql\pofolio\SIC;
use pofolio\dao\mysql\pofolio\Stock;
use pofolio\dao\mysql\pofolio\UpgradesDowngrades;
use pool\classes\Database\DataInterface;
use pool\classes\Database\Driver\MySQLi;
use pool\classes\Exception\RuntimeException;

\set_time_limit(0);

// the implicit flush is turned on, so you can see immediately the output
\ob_implicit_flush(1);

// get my process id
$pid = \getmypid();

if(!$pid) {
    die('Process ID couldn\'t be detected.');
}

// is console
const IS_CLI = \PHP_SAPI === 'cli';

if(IS_CLI) {
    $_SERVER['DOCUMENT_ROOT'] = '/virtualweb/manhart';
    if(!\is_dir($_SERVER['DOCUMENT_ROOT'])) die('Root directory '.$_SERVER['DOCUMENT_ROOT'].' is missing!');
    $_SERVER['SERVER_NAME'] = \gethostname(); // php_uname('n');
    $lineBreak = \chr(10);
    $options = \getopt('vh', ['list::']);
}
else {
    $lineBreak = '<br>';
    $options = $_REQUEST;
}
\define('LINE_BREAK', $lineBreak);

$symbol = \array_key_exists('list', $options) ? $options['list'] : '';

// include libs: POOL
const DIR_CONFIGS_ROOT = '/virtualweb/manhart/pofolio/config';
require_once DIR_CONFIGS_ROOT.'/config.inc.php'; // <-- innerhalb config.inc.php die Pfade anpassen!

// POOL integration
require_once DIR_POOL_ROOT.'/pool.lib.php';

// global composer packages
// require DIR_DOCUMENT_ROOT.'/manhart/pofolio/vendor/autoload.php';

$connectOptions = [
    'host' => MYSQL_HOST,
    'database' => [\DB_POFOLIO],
];
DataInterface::createDataInterface($connectOptions,  MySQLi::getInstance());
$StockDAO = Stock::create();
$stockTypes = $StockDAO->getColumnEnumValues('type');


$client = FmpApiClient::getInstance();

$symbol = 'AAPL';
stockImporter($client, $symbol);
shareFloatImporter($client, $symbol);
dividendImporter($client, $symbol);
priceTargetImporter($client, $symbol);
upgradesDowngradesImporter($client, $symbol);
historicalPriceImporter($client, $symbol);


$stockList = $client->getStockList();


foreach($stockList as $stock) {
    $symbol = $stockList->getSymbol();

    echo 'Symbol: '.$symbol.LINE_BREAK;
    /** @noinspection ForgottenDebugOutputInspection */
    \var_dump($stock);

    if(!\in_array($stockList->getType(), $stockTypes, true)) {
        throw new RuntimeException("Stock type {$stockList->getType()} is not valid!");
    }

    $idStock = stockImporter($client, $symbol, $stockList->getType(), $stockList->getExchangeShortName(), $stockList->getExchange());

    shareFloatImporter($client, $idStock);
    dividendImporter($client, $idStock);
    priceTargetImporter($client, $idStock);
    upgradesDowngradesImporter($client, $idStock);
    historicalPriceImporter($client, $idStock);

    refreshShareFloat($idStock);

    // Amount of requests
    $requests = $client->getRequests();
    echo 'Requests: '.$requests.LINE_BREAK;
    \flush();
}

/**
 * @param FmpApiClient $client
 * @param string $symbol
 * @param string|null $type
 * @param string|null $exchangeShortName
 * @param string|null $exchange
 * @return int
 */
function stockImporter(FmpApiClient $client, string $symbol, ?string $type = null, ?string $exchangeShortName = null, ?string $exchange = null): int
{
    $StockDAO = Stock::create();
    $ExchangeDAO = Exchange::create();
    $IndustryDAO = Industry::create();
    $SectorDAO = Sector::create();
    $CurrencyDAO = Currency::create();
    $CompanyDAO = Company::create();

    $Profile = $client->getProfile($symbol);

    echo 'Profile for: '.$symbol.LINE_BREAK;
    $Profile->dump();

    $CompanyCoreInformation = $client->getCompanyCoreInformation($symbol);
    echo 'CompanyCoreInformation for: '.$symbol.LINE_BREAK;
    $CompanyCoreInformation->dump();

    $type ??= $Profile->getType();
    $exchange ??= $Profile->getExchange();
    $exchangeShortName ??= $Profile->getExchangeShortName();

    // Exchange
    if(!$ExchangeDAO->exists($exchangeShortName)) {
        $idExchange = $ExchangeDAO->insert([
            'exchange' => $exchange,
            'exchangeShortName' => $exchangeShortName
        ])->getValueAsInt('last_insert_id');
    }
    else {
        $idExchange = $ExchangeDAO->setColumns('idExchange')->get($exchangeShortName, 'exchangeShortName')->getValueAsInt('idExchange');
    }

    // Industry
    $industry = $Profile->getIndustry();
    if($industry) {
        if(!$IndustryDAO->exists($industry)) {
            $idIndustry = $IndustryDAO->insert([
                'industry' => $industry
            ])->getValueAsInt('last_insert_id');
        }
        else {
            $idIndustry = $IndustryDAO->setColumns('idIndustry')->get($industry, 'industry')->getValueAsInt('idIndustry');
        }
    }

    // Sector
    $sector = $Profile->getSector();
    if($sector) {
        if(!$SectorDAO->exists($sector)) {
            $idSector = $SectorDAO->insert([
                'sector' => $sector
            ])->getValueAsInt('last_insert_id');
        }
        else {
            $idSector = $SectorDAO->setColumns('idSector')->get($sector, 'sector')->getValueAsInt('idSector');
        }
    }

    // SIC
    $sicCode = $CompanyCoreInformation->getSICCode();
    if($sicCode) {
        $SICDAO = SIC::create();
        if(!$SICDAO->exists($sicCode)) {
            $sicGroup = $CompanyCoreInformation->getSICGroup();
            $sicDescription = $CompanyCoreInformation->getSICDescription();
            $sicData = [
                'sicCode' => $sicCode,
                'sicGroup' => $sicGroup,
                'sicDescription' => $sicDescription,
            ];
            $SICDAO->insert($sicData);
        }
    }

    // Currency
    $currency = $Profile->getCurrency();
    if($currency) {
        if(!$CurrencyDAO->exists($currency)) {
            $idCurrency = $CurrencyDAO->insert([
                'currency' => $currency
            ])->getValueAsInt('last_insert_id');
        }
        else {
            $idCurrency = $CurrencyDAO->setColumns('idCurrency')->get($currency, 'currency')->getValueAsInt('idCurrency');
        }
    }

    // Stock
    $stockData = [
        'symbol' => $symbol,
        'type' => $type,
        'name' => $Profile->getCompanyName(),
        'idExchange' => $idExchange,
        'idCurrency' => $idCurrency ?? null,
        'ISIN' => $Profile->getISIN(),
        'tradeable' => $Profile->isActivelyTrading(),
        'CIK' => $Profile->getCIK() ?: null,
        'CUSIP' => $Profile->getCUSIP(),
        'idIndustry' => $idIndustry ?? null,
        'idSector' => $idSector ?? null,
        'ipoDate' => $Profile->getIpoDate(),
    ];

    if(!$StockDAO->exists($symbol)) {
        $Set = $StockDAO->insert($stockData);
        $idStock = $Set->getValueAsInt('last_insert_id');
    }
    else {
        $idStock = $StockDAO->setColumns('idStock')->get($symbol, 'symbol')->getValueAsInt('idStock');
        $stockData['idStock'] = $idStock;
        $Set = $StockDAO->update($stockData);
    }

    if($lastError = $Set->getLastError()) {
        throw new RuntimeException($lastError['message']);
    }

    [$fiscalYearEndMonth, $fiscalYearEndDay] = \explode('-', $CompanyCoreInformation->getFiscalYearEnd());

    // Company
    $companyData = [
        'idStock' => $idStock,
        'companyName' => $Profile->getCompanyName(),
        'address' => $Profile->getAddress(),
        'zip' => $Profile->getZip(),
        'city' => $Profile->getCity(),
        'state' => $Profile->getState(),
        'phone' => $Profile->getPhone(),
        'website' => $Profile->getWebsite(),
        'description' => $Profile->getDescription(),
        'ceo' => $Profile->getCEO(),
        'fullTimeEmployees' => $Profile->getFullTimeEmployees(),
        'image' => $Profile->getImage(),
        'sicCode' => $CompanyCoreInformation->getSICCode(),
        'fiscalYearEndDay' => (int)$fiscalYearEndDay,
        'fiscalYearEndMonth' => (int)$fiscalYearEndMonth,
        'registrantName' => $CompanyCoreInformation->getRegistrantName(),
        'taxIdentificationNumber' => $CompanyCoreInformation->getTaxIdentificationNumber(),
        'stateLocation' => $CompanyCoreInformation->getStateLocation(),
        'stateOfIncorporation' => $CompanyCoreInformation->getStateOfIncorporation(),
        'dcf' => $Profile->getDcf(),
    ];


    if($CompanyDAO->exists($idStock)) {
        $Set = $CompanyDAO->update($companyData);
    }
    else {
        $Set = $CompanyDAO->insert($companyData);
    }

    if($lastError = $Set->getLastError()) {
        throw new RuntimeException($lastError['message']);
    }

    // download image and save it to resources/images/stock/
    $image = $Profile->getImage();
    if($image) {
        $imagePath = DIR_DOCUMENT_ROOT.'/resources/images/stock/'.$symbol.'.png';
        if(!\file_exists($imagePath)) {
            $imageData = \file_get_contents($image);
            if($imageData !== false)
                \file_put_contents($imagePath, $imageData);
        }
    }
    return $idStock;
}

/**
 * @param int|string $symbol
 * @return array
 */
function extractSymbol(int|string $symbol): array
{
    if(\is_int($symbol)) {
        $idStock = $symbol;
        $symbol = Stock::create()->setColumns('symbol')->get($idStock, 'idStock')->getValueAsString('symbol');
    }
    else {
        $idStock = Stock::create()->setColumns('idStock')->get($symbol, 'symbol')->getValueAsInt('idStock');
    }
    return array($idStock, $symbol);
}

function shareFloatImporter(FmpApiClient $client, int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);

    $ShareFloatDAO = ShareFloat::create();
    $ShareFloat = $client->getShareFloat($symbol);
    echo 'ShareFloat for: '.$symbol.LINE_BREAK;
    $ShareFloat->dump();

    foreach($ShareFloat as $ignored) {
        $shareFloatData = [
            'idStock' => $idStock,
            'date' => $ShareFloat->getDate(),
            'floatShares' => $ShareFloat->getFloatShares(),
            'outstandingShares' => $ShareFloat->getOutstandingShares(),
            'freeFloat' => $ShareFloat->getFreeFloat(),
            'source' => $ShareFloat->getSource(),
        ];

        if(!$ShareFloatDAO->exists($idStock, $ShareFloat->getDate())) {
            $recordSet = $ShareFloatDAO->insert($shareFloatData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

function dividendImporter(FmpApiClient $client, int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);

    $DividendDAO = Dividend::create();
    $stockDividend = $client->getStockDividend($symbol);
    echo 'Dividends for: '.$symbol.LINE_BREAK;
    $stockDividend->dump();

    foreach($stockDividend as $ignored) {
        $dividendData = [
            'idStock' => $idStock,
            'date' => $stockDividend->getDate(),
            'adjDividend' => $stockDividend->getAdjDividend(),
            'dividend' => $stockDividend->getDividend(),
            'recordDate' => $stockDividend->getRecordDate(),
            'paymentDate' => $stockDividend->getPaymentDate(),
            'declarationDate' => $stockDividend->getDeclarationDate(),
        ];

        if(!$DividendDAO->exists($idStock, $stockDividend->getDate())) {
            $recordSet = $DividendDAO->insert($dividendData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

/**
 * @param FmpApiClient $client
 * @param int|string $symbol
 * @return void
 */
function priceTargetImporter(FmpApiClient $client, int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);

    $PriceTargetDAO = PriceTarget::create();
    $PriceTarget = $client->getPriceTarget($symbol);
    echo 'PriceTargets for: '.$symbol.LINE_BREAK;
    $PriceTarget->dump();

    foreach($PriceTarget as $ignored) {
        $priceTargetData = [
            'idStock' => $idStock,
            'publishedDate' => $PriceTarget->getPublishedDate(),
            'newsURL' => $PriceTarget->getNewsURL(),
            'newsTitle' => $PriceTarget->getNewsTitle(),
            'analystName' => $PriceTarget->getAnalystName(),
            'priceTarget' => $PriceTarget->getPriceTarget(),
            'adjPriceTarget' => $PriceTarget->getAdjPriceTarget(),
            'priceWhenPosted' => $PriceTarget->getPriceWhenPosted(),
            'newsPublisher' => $PriceTarget->getNewsPublisher(),
            'newsBaseURL' => $PriceTarget->getNewsBaseURL(),
            'analystCompany' => $PriceTarget->getAnalystCompany(),
        ];

        if(!$PriceTargetDAO->exists($idStock, $PriceTarget->getPublishedDate())) {
            $recordSet = $PriceTargetDAO->insert($priceTargetData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

function upgradesDowngradesImporter(FmpApiClient $client, string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);

    $UpgradesDowngradesDAO = UpgradesDowngrades::create();
    $UpgradesDowngrades = $client->getUpgradesDowngrades($symbol);
    echo 'UpgradesDowngrades for: '.$symbol.LINE_BREAK;
    $UpgradesDowngrades->dump();

    foreach($UpgradesDowngrades as $ignored) {
        $upgradesDowngradesData = [
            'idStock' => $idStock,
            'publishedDate' => $UpgradesDowngrades->getPublishedDate(),
            'newsURL' => $UpgradesDowngrades->getNewsURL(),
            'newsTitle' => $UpgradesDowngrades->getNewsTitle(),
            'newsBaseURL' => $UpgradesDowngrades->getNewsBaseURL(),
            'newsPublisher' => $UpgradesDowngrades->getNewsPublisher(),
            'newGrade' => $UpgradesDowngrades->getNewGrade(),
            'previousGrade' => $UpgradesDowngrades->getPreviousGrade(),
            'gradingCompany' => $UpgradesDowngrades->getGradingCompany(),
            'action' => $UpgradesDowngrades->getAction(),
            'priceWhenPosted' => $UpgradesDowngrades->getPriceWhenPosted(),
        ];

        if(!$UpgradesDowngradesDAO->exists($idStock, $UpgradesDowngrades->getPublishedDate())) {
            $recordSet = $UpgradesDowngradesDAO->insert($upgradesDowngradesData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

function historicalPriceImporter(FmpApiClient $client, string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $historicalPriceDAO = HistoricalPrice::create();
    $historicalPrice = $client->getHistoricalPrice($symbol, new \DateTime('-50 years'));
    echo 'HistoricalPrice for: '.$symbol.LINE_BREAK;
    $historicalPrice->dump();

    foreach($historicalPrice as $ignored)
    {
        if($historicalPriceDAO->exists($idStock, $historicalPrice->getDate())) {
            continue;
        }
        $historicalPriceData = [
            'idStock' => $idStock,
            'date' => $historicalPrice->getDate(),
            'open' => $historicalPrice->getOpen(),
            'high' => $historicalPrice->getHigh(),
            'low' => $historicalPrice->getLow(),
            'close' => $historicalPrice->getClose(),
            'adjClose' => $historicalPrice->getAdjClose(),
            'volume' => $historicalPrice->getVolume(),
            'unadjustedVolume' => $historicalPrice->getUnadjustedVolume(),
            'change' => $historicalPrice->getChange(),
            'changePercent' => $historicalPrice->getChangePercent(),
            'vwap' => $historicalPrice->getVwap(),
            'changeOverTime' => $historicalPrice->getChangeOverTime(),
        ];
        $recordSet = $historicalPriceDAO->insert($historicalPriceData);
        if($lastError = $recordSet->getLastError()) {
            throw new RuntimeException($lastError['message'], $lastError['code']);
        }
    }
}

function refreshShareFloat(int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $shareFloatDAO = ShareFloat::create();
    $recordSet = $shareFloatDAO->getMultiple(filter_rules: [['idStock', 'equal', $idStock]], sorting: ['date' => 'DESC'], limit: [1]);
    if($lastError = $recordSet->getLastError()) {
        throw new RuntimeException($lastError['message'], $lastError['code']);
    }

    if(\count($recordSet)) {
        $stockDAO = Stock::create();
        $stockData = [
            'idStock' => $idStock,
            'freeFloat' => $recordSet->getValueAsFloat('freeFloat'),
            'floatShares' => $recordSet->getValueAsInt('floatShares'),
            'outstandingShares' => $recordSet->getValueAsInt('outstandingShares'),
        ];
        $recordSet = $stockDAO->update($stockData);
        if($lastError = $recordSet->getLastError()) {
            throw new RuntimeException($lastError['message'], $lastError['code']);
        }
    }
}