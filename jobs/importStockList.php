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
use pofolio\dao\mysql\pofolio\Country;
use pofolio\dao\mysql\pofolio\Currency;
use pofolio\dao\mysql\pofolio\Dividend;
use pofolio\dao\mysql\pofolio\Exchange;
use pofolio\dao\mysql\pofolio\HistoricalPrice;
use pofolio\dao\mysql\pofolio\IncomeStatement;
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
    if(!\is_dir($_SERVER['DOCUMENT_ROOT']))
        die('Root directory '.$_SERVER['DOCUMENT_ROOT'].' is missing!');
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

// truncate all tables
//ShareFloat::create()->truncate();
//Dividend::create()->truncate();
//PriceTarget::create()->truncate();
//UpgradesDowngrades::create()->truncate();
//HistoricalPrice::create()->truncate();
//Company::create()->truncate();
//Stock::create()->truncate();
//Exchange::create()->truncate();
//Industry::create()->truncate();
//Sector::create()->truncate();
//Currency::create()->truncate();
//SIC::create()->truncate();



$symbols = [/*'INTC', 'HOT.DE', '639.DE', 'EA', 'NEM.DE', 'DE', 'AZN.L', 'TMV.DE', 'LMT', 'BHP', 'UBER', '3RB.DE', '2587.T', 'CVS', 'TPE.DE', 'SHF.DE',
    'DHI', 'DEMANT.CO', 'QSR', 'PANW', 'MCD', '5108.T', 'ORCL', 'QLYS', '11B.WA', 'MAR', 'PDX.ST', 'NTNX', 'NKE', 'TOM.OL', 'TTE.PA', 'AVGO', 'NEM',
    'ALV.DE', 'NOC', '0941.HK', 'IAC', '3662.HK', 'CHGG', 'APPS', 'COMP', 'LPSN', 'FNKO', 'FSLY', 'COIN', 'CRNC', 'SMWB', 'ZBRA', 'ILMN', 'MPW',
    'BPOST.BR', '5CP.SI', 'CDR.WA', 'AMS.SW', '013A.F', '1044.HK', 'M0YN.DE', 'MRK.DE', 'DHL.DE', 'CCC3.DE', 'AIR.DE', 'BAKKA.OL', 'AMGN', 'GFT.DE',
    'SAM', 'PAYX', 'ECV.DE',*/ 'WPM', 'META', 'SIE.DE', 'INVE-A.ST', 'INVE-B.ST', 'JKHY', 'PSTG', 'MUV2.DE', 'SALM.OL', 'INFY.NS', 'GOOGL', 'MTX.DE',
    'ATS.VI', 'FDS', 'NXU.DE', 'ADSK', 'AAD.DE', 'BC8.DE', 'MDO.DE', 'ANET', 'EVD.DE', 'ZS', 'CMG', 'MSFT', 'ADBE', 'NVO', 'NOVO-B.CO', 'NOVC.DE',
    'DMRE.DE', 'KTN.DE', 'TTD', 'NVDA', 'AMZN'];
foreach($symbols as $symbol) {
    try {
        stockImporter($client, $symbol);
    }
    catch(RuntimeException $e) {
        echo $e->getMessage().LINE_BREAK;
        continue;
    }

    shareFloatImporter($client, $symbol);
    dividendImporter($client, $symbol);
    priceTargetImporter($client, $symbol);
    upgradesDowngradesImporter($client, $symbol);
    historicalPriceImporter($client, $symbol);
    incomeStatementImporter($client, $symbol);
//    break;
}


delistedCompaniesImporter($client);
die();
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
 * @throws RuntimeException
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

    if(!$Profile->hasResponse()) {
        throw new RuntimeException("No profile data available for symbol $symbol");
    }

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

    // Country
    $country = $Profile->getCountry();
    $idCountry = Country::create()->setColumns('idCountry')->get($country, 'isoCode')->getValueAsInt('idCountry') ?: null;

    // Stock
    $stockData = [
        'symbol' => $symbol,
        'type' => $type,
        'price' => $Profile->getPrice(),
        'name' => $Profile->getCompanyName(),
        'idExchange' => $idExchange,
        'idCurrency' => $idCurrency ?? null,
        'ISIN' => $Profile->getISIN(),
        'tradeable' => $Profile->isActivelyTrading(),
        'CIK' => $Profile->getCIK() ?: null,
        'CUSIP' => $Profile->getCUSIP(),
        'idIndustry' => $idIndustry ?? null,
        'idSector' => $idSector ?? null,
        'idCountry' => $idCountry,
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
        $destImagePath = DIR_DOCUMENT_ROOT . '/resources/images/stock/';
        mkdirs($destImagePath);
        $ext = file_extension($image);
        $logo = "$destImagePath$symbol.$ext";
        if(!\file_exists($logo)) {
            $imageData = \file_get_contents($image);
            if($imageData !== false)
                \file_put_contents($logo, $imageData);
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
            'floatShares' => $lastFloatShares ??= $ShareFloat->getFloatShares(),
            'outstandingShares' => $lastOutstandingShares ??= $ShareFloat->getOutstandingShares(),
            'freeFloat' => $lastFreeFloat ??= $ShareFloat->getFreeFloat(),
            'source' => $ShareFloat->getSource(),
        ];

        if(!$ShareFloatDAO->exists($idStock, $ShareFloat->getDate())) {
            $recordSet = $ShareFloatDAO->insert($shareFloatData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }

    // update stock
    $stockDAO = Stock::create();
    $stockData = [
        'idStock' => $idStock,
        'lastFreeFloat' => $lastFreeFloat ?? null,
        'lastFloatShares' => $lastFloatShares ?? null,
        'lastOutstandingShares' => $lastOutstandingShares ?? null,
    ];
    $recordSet = $stockDAO->update($stockData);
    if($lastError = $recordSet->getLastError()) {
        throw new RuntimeException($lastError['message'], $lastError['code']);
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

function incomeStatementImporter(FmpApiClient $client, string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $incomeStatementAnnual = $client->getIncomeStatement($symbol);
    echo 'IncomeStatement for: '.$symbol.LINE_BREAK;
    $incomeStatementAnnual->dump();

    $incomeStatementQuarter = $client->getIncomeStatement($symbol, \pofolio\classes\FMP\Response\IncomeStatement::PERIOD_QUARTER);
    // dump not necessary

    $importIncomeStatement = static function(
        \pofolio\classes\FMP\Response\IncomeStatement $incomeStatement,
        mixed $idStock,
        string $symbol
    ): void {
        $incomeStatementDAO = IncomeStatement::create();
        $currencyDAO = Currency::create();
        foreach($incomeStatement as $ignored) {

            $idReportedCurrency = $currencyDAO->setColumns('idCurrency')->get($incomeStatement->getReportedCurrency(),
                'currency')->getValueAsInt('idCurrency') ?:
                Stock::create()->setColumns('idCurrency')->get($idStock, 'idStock')->getValueAsInt('idCurrency');

            $periodOrder = match ($incomeStatement->getPeriod()) {
                IncomeStatement::PERIOD_QUARTER1 => 1,
                IncomeStatement::PERIOD_QUARTER2 => 2,
                IncomeStatement::PERIOD_QUARTER3 => 3,
                IncomeStatement::PERIOD_QUARTER4 => 4,
                IncomeStatement::PERIOD_FISCAL_YEAR => 0,
                default => throw new RuntimeException('Unknown period: ' . $incomeStatement->getPeriod()),
            };

            $calendarYear = $incomeStatement->getCalendarYear();
            $period = $incomeStatement->getPeriod();
            $EPS = $incomeStatement->getEPS();
            $EPSDiluted = $incomeStatement->getEPSDiluted();
            if($symbol === 'BHP' && $calendarYear === 2016 && $EPSDiluted === 1063134.16)
                $EPSDiluted = 1.06;
            if($symbol === 'BHP' && $calendarYear === 2013 && $EPSDiluted === 7588.47)
                $EPSDiluted = $EPS;
            // if the difference between EPS and EPSDiluted is greater than 1, then there is a problem with the data
            if($EPS && $EPSDiluted && \abs($EPS - $EPSDiluted) > 10) {
                throw new RuntimeException("EPS and EPSDiluted are very different $EPS vs $EPSDiluted for symbol $symbol in $calendarYear-$period!");
            }
            $EBITDARatio = $incomeStatement->getEBITDARatio();

            $incomeStatementData = [
                'idStock' => $idStock,
                'date' => $incomeStatement->getDate(),
                'idReportedCurrency' => $idReportedCurrency,
                'fillingDate' => $incomeStatement->getFillingDate(),
                'acceptedDate' => $incomeStatement->getAcceptedDate(),
                'calendarYear' => $calendarYear,
                'timePeriod' => $period,
                'periodOrder' => $periodOrder,
                'revenue' => $incomeStatement->getRevenue(),
                'costOfRevenue' => $incomeStatement->getCostOfRevenue(),
                'grossProfit' => $incomeStatement->getGrossProfit(),
                'grossProfitRatio' => $incomeStatement->getGrossProfitRatio(),
                'researchAndDevelopmentExpenses' => $incomeStatement->getResearchAndDevelopmentExpenses(),
                'generalAndAdministrativeExpenses' => $incomeStatement->getGeneralAndAdministrativeExpenses(),
                'sellingAndMarketingExpenses' => $incomeStatement->getSellingAndMarketingExpenses(),
                'sellingGeneralAndAdministrativeExpenses' => $incomeStatement->getSellingGeneralAndAdministrativeExpenses(),
                'otherExpenses' => $incomeStatement->getOtherExpenses(),
                'operatingExpenses' => $incomeStatement->getOperatingExpenses(),
                'costAndExpenses' => $incomeStatement->getCostAndExpenses(),
                'interestIncome' => $incomeStatement->getInterestIncome(),
                'interestExpense' => $incomeStatement->getInterestExpense(),
                'depreciationAndAmortization' => $incomeStatement->getDepreciationAndAmortization(),
                'EBITDA' => $incomeStatement->getEBITDA(),
                'EBITDARatio' => $EBITDARatio,
                'operatingIncome' => $incomeStatement->getOperatingIncome(),
                'operatingIncomeRatio' => $incomeStatement->getOperatingIncomeRatio(),
                'totalOtherIncomeExpensesNet' => $incomeStatement->getTotalOtherIncomeExpensesNet(),
                'incomeBeforeTax' => $incomeStatement->getIncomeBeforeTax(),
                'incomeBeforeTaxRatio' => $incomeStatement->getIncomeBeforeTaxRatio(),
                'incomeTaxExpense' => $incomeStatement->getIncomeTaxExpense(),
                'netIncome' => $incomeStatement->getNetIncome(),
                'netIncomeRatio' => $incomeStatement->getNetIncomeRatio(),
                'EPS' => $EPS,
                'EPSDiluted' => $EPSDiluted,
                'weightedAverageShsOut' => $incomeStatement->getWeightedAverageShsOut(),
                'weightedAverageShsOutDil' => $incomeStatement->getWeightedAverageShsOutDil(),
                'link' => $incomeStatement->getLink(),
                'finalLink' => $incomeStatement->getFinalLink(),
            ];

            if($incomeStatementDAO->exists($idStock, $incomeStatement->getCalendarYear(), $incomeStatement->getPeriod())) {
                $filter_rules = [
                    [$idStock, 'equal', 'idStock'],
                    ['calendarYear', 'equal', $incomeStatement->getCalendarYear()],
                    ['timePeriod', 'equal', $incomeStatement->getPeriod()]
                ];
                $idIncomeStatement = $incomeStatementDAO->setColumns('idIncomeStatement')->getMultiple(filter_rules: $filter_rules)->getValueAsInt('idIncomeStatement');
                $incomeStatementData['idIncomeStatement'] = $idIncomeStatement;
                $recordSet = $incomeStatementDAO->update($incomeStatementData);
            }
            else {
                $recordSet = $incomeStatementDAO->insert($incomeStatementData);
            }
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    };

    $importIncomeStatement($incomeStatementAnnual, $idStock, $symbol);
    $importIncomeStatement($incomeStatementQuarter, $idStock, $symbol);
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

function delistedCompaniesImporter(FmpApiClient $client): void
{
    $delistedCompanies = $client->getDelistedCompanies();
    echo 'DelistedCompanies: '.LINE_BREAK;
    $delistedCompanies->dump();

    foreach($delistedCompanies as $ignored) {
        $symbol = $delistedCompanies->getSymbol();
        $stock = Stock::create();
        if(!$stock->exists($symbol))
            continue;

        $idStock = $stock->setColumns('idStock')->get($symbol, 'symbol')->getValueAsInt('idStock');

        $delistedCompaniesData = [
            'idStock' => $idStock,
            'delistedDate' => $delistedCompanies->getDelistedDate(),
        ];

        if($lastError = $stock->update($delistedCompaniesData)->getLastError()) {
            throw new RuntimeException($lastError['message'], $lastError['code']);
        }
    }
}