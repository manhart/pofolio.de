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
use pofolio\dao\mysql\pofolio\BalanceSheetStatement;
use pofolio\dao\mysql\pofolio\CashflowStatement;
use pofolio\dao\mysql\pofolio\Company;
use pofolio\dao\mysql\pofolio\Country;
use pofolio\dao\mysql\pofolio\Currency;
use pofolio\dao\mysql\pofolio\Dividend;
use pofolio\dao\mysql\pofolio\DividendCalendar;
use pofolio\dao\mysql\pofolio\Exchange;
use pofolio\dao\mysql\pofolio\HistoricalPrice;
use pofolio\dao\mysql\pofolio\IncomeStatement;
use pofolio\dao\mysql\pofolio\Industry;
use pofolio\dao\mysql\pofolio\PriceTarget;
use pofolio\dao\mysql\pofolio\Sector;
use pofolio\dao\mysql\pofolio\ShareFloat;
use pofolio\dao\mysql\pofolio\SIC;
use pofolio\dao\mysql\pofolio\Split;
use pofolio\dao\mysql\pofolio\SplitCalendar;
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

$search = $client->getCIK(1067983);
//$search->dump();
while($search->valid()) {
    echo 'CIK: '.$search->getCIK().LINE_BREAK;
    echo 'Name: '.$search->getName().LINE_BREAK;
    $search->next();
}
echo 'Num results: '.count($search).LINE_BREAK;

exit;

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



$symbols = array_unique(['ALRM', 'UMI.BR', 'TNC', 'CRM', 'TEVA.TA', 'MBB.DE', 'KWS.DE', 'UHS', 'POS.VI', 'HIMS', 'VRNS', 'APH', 'COP.DE', 'UTDI.DE', 'AEM', 'MYTAY',
    'UNA.AS', 'HEIA.AS', 'CMC.DE', '0066.HK', 'DG.PA', 'TSM', 'EXTR', 'NOEJ.DE', 'IBC3.F', 'IS3N.DE', 'SAVE', 'UPST', 'RH', 'ENR.DE', 'WBD', '2HRA.DE',
    'IBC3.DE', 'ABR', 'BNR.DE', 'PRX.AS', 'KRN.DE', 'WDC', 'LSG.OL', 'HFG.DE', 'CTSH', 'SWKS', 'DBX', 'SSD', 'EUZ.DE', '690D.DE', 'BDT.DE', 'GIS',
    'PND.F', 'MOWI.OL', 'RI.PA', 'MAR', 'INTC', 'HOT.DE', '639.DE', 'EA', 'NEM.DE', 'DE', 'AZN.L', 'TMV.DE', 'LMT', 'BHP', 'UBER', '3RB.DE', '2587.T',
    'CVS', 'TPE.DE', 'SHF.DE', 'ABEA.DE', 'AMD', 'PEP', 'SAP.DE', 'MKL', 'KHC', 'STO3.DE', 'JNJ', 'SHELL.AS', 'AMS.MC', 'FRA.DE', 'IBM', '8TRA.DE',
    'DHI', 'DEMANT.CO', 'QSR', 'PANW', 'MCD', '5108.T', 'ORCL', 'QLYS', '11B.WA', 'PDX.ST', 'NTNX', 'NKE', 'TOM.OL', 'TTE.PA', 'AVGO', 'NEM',
    'ALV.DE', 'NOC', '0941.HK', 'IAC', '3662.HK', 'CHGG', 'APPS', 'COMP', 'LPSN', 'FNKO', 'FSLY', 'COIN', 'CRNC', 'SMWB', 'ZBRA', 'ILMN', 'MPW', 'EOAN.DE',
    'BPOST.BR', '5CP.SI', 'CDR.WA', 'AMS.SW', '013A.F', '1044.HK', 'M0YN.DE', 'MRK.DE', 'DHL.DE', 'CCC3.DE', 'AIR.DE', 'BAKKA.OL', 'AMGN', 'GFT.DE',
    'SAM', 'PAYX', 'ECV.DE', 'WPM', 'META', 'SIE.DE', 'INVE-A.ST', 'INVE-B.ST', 'JKHY', 'PSTG', 'MUV2.DE', 'SALM.OL', 'INFY.NS', 'GOOGL', 'MTX.DE',
    'ATS.VI', 'FDS', 'NXU.DE', 'ADSK', 'AAD.DE', 'BC8.DE', 'MDO.DE', 'ANET', 'EVD.DE', 'ZS', 'CMG', 'MSFT', 'ADBE', 'NVO', 'NOVO-B.CO', 'NOVC.DE',
    'DMRE.DE', 'KTN.DE', 'TTD', 'NVDA', 'AMZN', '9618.HK', 'IFF', 'ACC.OL', 'ROKU', 'KLG', 'ESTC', 'FRE.DE', 'QCOM', 'KGX.DE', '1177.HK', 'OSP2.DE',
    'VOW.DE', 'VOW3.DE', '9988.HK', 'BABA', 'MTCH', 'BBZA.DE', 'BION.SW', 'NNND.F', '0700.HK', 'GSHD', 'CTM.F', 'KEYS', 'EFX', 'LXH', 'AUTO.OL', 'PFE',
    'BAYN.DE', 'PYPL', 'SSTK', 'DIS', 'NDX1.DE', 'YAR.OL', 'U', 'KNEBV.HE', 'T', 'ADN1.DE', 'SYF', 'BMY', 'PUM.DE', 'AGCO', 'P911.HM', 'PAH3.DE', 'P911.DE',
    'BAS.DE', 'GS7.DE', 'K', 'BMT.DE', 'MARR.MI', 'TSN', '2GB.DE', 'MDT', 'SWK', 'MMM', 'CMC', 'ABI.BR', '1NBA.DE', 'FPE.DE', 'MOL.BD', 'MYTAY', 'AEM.TO',
    'LVS', '7309.T', 'VVSM.DE', 'IBKR', 'ST5.DE', 'HEN.DE', 'HEN3.DE', '1TY.DE', 'NVJP.DE', 'DOCN', 'NGLB.DE', 'AAL.L', 'GSF.OL', 'B1C.F', '9888.HK',
    'B5A.DE', 'AR4.DE', 'COK.DE', 'KCO.DE', '3690.HK', 'AYX', 'SSYS', 'COMP', 'SEDG']);

//$symbols = ['TTD'];

foreach($symbols as $symbol) {
    try {
        stockImporter($client, $symbol);
    }
    catch(RuntimeException $e) {
        echo $e->getMessage().LINE_BREAK;
        continue;
    }

//    shareFloatImporter($client, $symbol);
//    dividendImporter($client, $symbol);
//    priceTargetImporter($client, $symbol);
//    upgradesDowngradesImporter($client, $symbol);
//    historicalPriceImporter($client, $symbol);
//    incomeStatementImporter($client, $symbol);
//    balanceSheetStatementImporter($client, $symbol);
//    cashflowStatementImporter($client, $symbol);
    splitImporter($client, $symbol);
}


delistedCompaniesImporter($client);
dividendCalendarImporter($client, new \DateTime('-1 month'), new \DateTime('+2 month'));
dividendCalendarImporter($client, new \DateTime('+2 month'), new \DateTime('+4 month'));
dividendCalendarImporter($client, new \DateTime('+4 month'), new \DateTime('+6 month'));

splitCalendarImporter($client, new \DateTime('-1 month'), new \DateTime('+2 month'));
splitCalendarImporter($client, new \DateTime('+2 month'), new \DateTime('+4 month'));
splitCalendarImporter($client, new \DateTime('+4 month'), new \DateTime('+6 month'));
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
    splitImporter($client, $idStock);
    priceTargetImporter($client, $idStock);
    upgradesDowngradesImporter($client, $idStock);
    historicalPriceImporter($client, $idStock);
    incomeStatementImporter($client, $idStock);
    balanceSheetStatementImporter($client, $idStock);

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
    $idCountry = null;
    if($country) {
        $idCountry = Country::create()->setColumns('idCountry')->get($country, 'isoCode')->getValueAsInt('idCountry') ?: null;
    }

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
        'CIK' => $Profile->getCIKAsInt() ?: null,
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

    $fiscalYearEnd = $CompanyCoreInformation->getFiscalYearEnd();
    $fiscalYearEndDay = null;
    $fiscalYearEndMonth = null;
    if($fiscalYearEnd) {
        [$fiscalYearEndMonth, $fiscalYearEndDay] = \explode('-', $fiscalYearEnd);
    }

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
            'label' => $stockDividend->getLabel(),
        ];

        if(!$DividendDAO->exists($idStock, $stockDividend->getDate())) {
            $recordSet = $DividendDAO->insert($dividendData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

function dividendCalendarImporter(FmpApiClient $client, \DateTimeInterface $from, \DateTimeInterface $to): void
{
    $dividendCalendarDAO = DividendCalendar::create();
    $dividendCalendarResponse = $client->getStockDividendCalendar($from, $to);
    echo 'DividendCalendar: '.LINE_BREAK;
    $dividendCalendarResponse->dump();

    $stockDAO = Stock::create()->setColumns('idStock');
    foreach($dividendCalendarResponse as $ignored) {
        $symbol = $dividendCalendarResponse->getSymbol();
        $idStock = $stockDAO->get($symbol, 'symbol')->getValueAsInt('idStock');
        if(!$idStock) { // not imported yet
            continue;
        }
        $dividendCalendarData = [
            'idStock' => $idStock,
            'date' => $dividendCalendarResponse->getDate(),
            'adjDividend' => $dividendCalendarResponse->getAdjDividend(),
            'dividend' => $dividendCalendarResponse->getDividend(),
            'recordDate' => $dividendCalendarResponse->getRecordDate(),
            'paymentDate' => $dividendCalendarResponse->getPaymentDate(),
            'declarationDate' => $dividendCalendarResponse->getDeclarationDate(),
            'label' => $dividendCalendarResponse->getLabel(),
        ];

        if(!$dividendCalendarDAO->exists($idStock, $dividendCalendarResponse->getDate())) {
            $recordSet = $dividendCalendarDAO->insert($dividendCalendarData);
        }
        else {
            $idDividendCalendar = $dividendCalendarDAO->setColumns('idDividendCalendar')->getMultiple(filter: [['idStock', 'equal', $idStock], ['date', 'equal', $dividendCalendarResponse->getDate()]])->getValueAsInt('idDividendCalendar');
            $dividendCalendarData['idDividendCalendar'] = $idDividendCalendar;
            $recordSet = $dividendCalendarDAO->update($dividendCalendarData);
        }
        if($lastError = $recordSet->getLastError()) {
            throw new RuntimeException($lastError['message'], $lastError['code']);
        }
    }
}

function splitImporter(FmpApiClient $client, int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);

    $SplitDAO = Split::create();
    $stockSplit = $client->getStockSplit($symbol);
    echo 'Splits for: '.$symbol.LINE_BREAK;
    $stockSplit->dump();

    foreach($stockSplit as $ignored) {
        $dividendData = [
            'idStock' => $idStock,
            'date' => $stockSplit->getDate(),
            'numerator' => $stockSplit->getNumerator(),
            'denominator' => $stockSplit->getDenominator(),
        ];

        if(!$SplitDAO->exists($idStock, $stockSplit->getDate())) {
            $recordSet = $SplitDAO->insert($dividendData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    }
}

function splitCalendarImporter(FmpApiClient $client, \DateTimeInterface $from, \DateTimeInterface $to): void
{
    $splitCalendarDAO = SplitCalendar::create();
    $splitCalendarResponse = $client->getStockSplitCalendar($from, $to);
    echo 'SplitCalendar: '.LINE_BREAK;
    $splitCalendarResponse->dump();

    $stockDAO = Stock::create()->setColumns('idStock');
    foreach($splitCalendarResponse as $ignored) {
        $symbol = $splitCalendarResponse->getSymbol();
        $idStock = $stockDAO->get($symbol, 'symbol')->getValueAsInt('idStock');
        if(!$idStock) { // not imported yet
            continue;
        }
        $splitCalendarData = [
            'idStock' => $idStock,
            'date' => $splitCalendarResponse->getDate(),
            'numerator' => $splitCalendarResponse->getNumerator(),
            'denominator' => $splitCalendarResponse->getDenominator(),
        ];

        if(!$splitCalendarDAO->exists($idStock, $splitCalendarResponse->getDate())) {
            $recordSet = $splitCalendarDAO->insert($splitCalendarData);
        }
        else {
            $idSplitCalendar = $splitCalendarDAO->setColumns('idDividendCalendar')->getMultiple(filter: [['idStock', 'equal', $idStock], ['date', 'equal', $splitCalendarResponse->getDate()]])->getValueAsInt('idDividendCalendar');
            $splitCalendarData['idDividendCalendar'] = $idSplitCalendar;
            $recordSet = $splitCalendarDAO->update($splitCalendarData);
        }
        if($lastError = $recordSet->getLastError()) {
            throw new RuntimeException($lastError['message'], $lastError['code']);
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
    // yesterday
    ($yesterday = new \DateTime('-1 day'))->setTime(0, 0);

    // the day before yesterday
    ($dayBeforeYesterday = new \DateTime('-2 day'))->setTime(0, 0);


    [$idStock, $symbol] = extractSymbol($symbol);
    $historicalPriceDAO = HistoricalPrice::create();
    $historicalPrice = $client->getHistoricalPrice($symbol, new \DateTime('-50 years'));
    echo 'HistoricalPrice for: '.$symbol.LINE_BREAK;
    $historicalPrice->dump();

    foreach($historicalPrice as $ignored) {

        // if date of historical price is yesterday, then update stock with previous close
        $previousClosingPrice = null;
        if($historicalPrice->getDate() == $yesterday) {
            $previousClosingPrice = $historicalPrice->getClose();
        }

        if($previousClosingPrice && $historicalPrice->getDate() == $dayBeforeYesterday) {
            $stockDAO = Stock::create();
            $stockData = [
                'idStock' => $idStock,
                'previousClose' => $historicalPrice->getClose(),
                'changePercent' => $previousClosingPrice / $historicalPrice->getClose() * 100 - 100,
                'change' => $previousClosingPrice - $historicalPrice->getClose(),
            ];
            $recordSet = $stockDAO->update($stockData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }


        if($historicalPriceDAO->exists($idStock, $historicalPrice->getDate())) {
            continue;
        }
        $open = $historicalPrice->getOpen();
        $high = $historicalPrice->getHigh();
        $low = $historicalPrice->getLow();
        $close = $historicalPrice->getClose();

        $adjClose = $historicalPrice->getAdjClose();
        if($adjClose < 0) { // if adjClose is negative, then it is wrong
            $adjClose = $close;
        }

        $historicalPriceData = [
            'idStock' => $idStock,
            'date' => $historicalPrice->getDate(),
            'open' => $open,
            'high' => $high,
            'low' => $low,
            'close' => $close,
            'adjClose' => $adjClose,
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
        // if date of historical price is yesterday, then update stock with previous close
        if($historicalPrice->getDate() == $yesterday) {
            $stockDAO = Stock::create();
            $stockData = [
                'idStock' => $idStock,
                'previousClose' => $historicalPrice->getClose(),
                'changePercent' => $historicalPrice->getChangePercent(),
                'change' => $historicalPrice->getChange(),
            ];
            $recordSet = $stockDAO->update($stockData);
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
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

            // if the difference between EPS and EPSDiluted is greater than 10 percent, then there is a problem with the data
            if($EPS && $EPSDiluted) {
                $difference = \abs($EPS - $EPSDiluted);
                $average = ($EPS + $EPSDiluted) / 2;
                $percent = $difference / $average * 100;

                $absoluteDifferenceThreshold = 3;

                if($percent > 100 && $difference > $absoluteDifferenceThreshold) {
                    throw new RuntimeException("EPS and EPSDiluted are very different $EPS vs $EPSDiluted for symbol $symbol in $calendarYear-$period!");
                }
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
                $idIncomeStatement = $incomeStatementDAO->setColumns('idIncomeStatement')->getMultiple(filter: $filter_rules)->getValueAsInt('idIncomeStatement');
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

function balanceSheetStatementImporter(FmpApiClient $client, string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $balanceSheetStatementAnnual = $client->getBalanceSheetStatement($symbol);
    echo 'BalanceSheetStatement for: '.$symbol.LINE_BREAK;
    $balanceSheetStatementAnnual->dump();

    $balanceSheetStatementQuarterly = $client->getBalanceSheetStatement($symbol, \pofolio\classes\FMP\Response\BalanceSheetStatement::PERIOD_QUARTER);
    // dump not necessary

    $importBalanceSheetStatement = static function(
        \pofolio\classes\FMP\Response\BalanceSheetStatement $balanceSheetStatement,
        mixed $idStock,
        string $symbol
    ): void {
        $balanceSheetStatementDAO = BalanceSheetStatement::create();
        $currencyDAO = Currency::create();
        foreach($balanceSheetStatement as $ignored) {

            $idReportedCurrency = $currencyDAO->setColumns('idCurrency')->get($balanceSheetStatement->getReportedCurrency(),
                'currency')->getValueAsInt('idCurrency') ?:
                Stock::create()->setColumns('idCurrency')->get($idStock, 'idStock')->getValueAsInt('idCurrency');

            $periodOrder = match ($balanceSheetStatement->getPeriod()) {
                BalanceSheetStatement::PERIOD_QUARTER1 => 1,
                BalanceSheetStatement::PERIOD_QUARTER2 => 2,
                BalanceSheetStatement::PERIOD_QUARTER3 => 3,
                BalanceSheetStatement::PERIOD_QUARTER4 => 4,
                BalanceSheetStatement::PERIOD_FISCAL_YEAR => 0,
                default => throw new RuntimeException('Unknown period: ' . $balanceSheetStatement->getPeriod()),
            };

            $calendarYear = $balanceSheetStatement->getCalendarYear();
            $period = $balanceSheetStatement->getPeriod();

            $balanceSheetStatementData = [
                'idStock' => $idStock,
                'date' => $balanceSheetStatement->getDate(),
                'idReportedCurrency' => $idReportedCurrency,
                'fillingDate' => $balanceSheetStatement->getFillingDate(),
                'acceptedDate' => $balanceSheetStatement->getAcceptedDate(),
                'calendarYear' => $calendarYear,
                'timePeriod' => $period,
                'periodOrder' => $periodOrder,
                'cashAndCashEquivalents' => $balanceSheetStatement->getCashAndCashEquivalents(),
                'shortTermInvestments' => $balanceSheetStatement->getShortTermInvestments(),
                'cashAndShortTermInvestments' => $balanceSheetStatement->getCashAndShortTermInvestments(),
                'netReceivables' => $balanceSheetStatement->getNetReceivables(),
                'inventory' => $balanceSheetStatement->getInventory(),
                'otherCurrentAssets' => $balanceSheetStatement->getOtherCurrentAssets(),
                'totalCurrentAssets' => $balanceSheetStatement->getTotalCurrentAssets(),
                'propertyPlantEquipmentNet' => $balanceSheetStatement->getPropertyPlantEquipmentNet(),
                'goodwill' => $balanceSheetStatement->getGoodwill(),
                'intangibleAssets' => $balanceSheetStatement->getIntangibleAssets(),
                'goodwillAndIntangibleAssets' => $balanceSheetStatement->getGoodwillAndIntangibleAssets(),
                'longTermInvestments' => $balanceSheetStatement->getLongTermInvestments(),
                'taxAssets' => $balanceSheetStatement->getTaxAssets(),
                'otherNonCurrentAssets' => $balanceSheetStatement->getOtherNonCurrentAssets(),
                'totalNonCurrentAssets' => $balanceSheetStatement->getTotalNonCurrentAssets(),
                'otherAssets' => $balanceSheetStatement->getOtherAssets(),
                'totalAssets' => $balanceSheetStatement->getTotalAssets(),
                'accountPayables' => $balanceSheetStatement->getAccountPayables(),
                'shortTermDebt' => $balanceSheetStatement->getShortTermDebt(),
                'taxPayables' => $balanceSheetStatement->getTaxPayables(),
                'deferredRevenue' => $balanceSheetStatement->getDeferredRevenue(),
                'otherCurrentLiabilities' => $balanceSheetStatement->getOtherCurrentLiabilities(),
                'totalCurrentLiabilities' => $balanceSheetStatement->getTotalCurrentLiabilities(),
                'longTermDebt' => $balanceSheetStatement->getLongTermDebt(),
                'deferredRevenueNonCurrent' => $balanceSheetStatement->getDeferredRevenueNonCurrent(),
                'deferredTaxLiabilitiesNonCurrent' => $balanceSheetStatement->getDeferredTaxLiabilitiesNonCurrent(),
                'otherNonCurrentLiabilities' => $balanceSheetStatement->getOtherNonCurrentLiabilities(),
                'totalNonCurrentLiabilities' => $balanceSheetStatement->getTotalNonCurrentLiabilities(),
                'otherLiabilities' => $balanceSheetStatement->getOtherLiabilities(),
                'capitalLeaseObligations' => $balanceSheetStatement->getCapitalLeaseObligations(),
                'totalLiabilities' => $balanceSheetStatement->getTotalLiabilities(),
                'preferredStock' => $balanceSheetStatement->getPreferredStock(),
                'commonStock' => $balanceSheetStatement->getCommonStock(),
                'retainedEarnings' => $balanceSheetStatement->getRetainedEarnings(),
                'accumulatedOtherComprehensiveIncomeLoss' => $balanceSheetStatement->getAccumulatedOtherComprehensiveIncomeLoss(),
                'othertotalStockholdersEquity' => $balanceSheetStatement->getOthertotalStockholdersEquity(),
                'totalStockholdersEquity' => $balanceSheetStatement->getTotalStockholdersEquity(),
                'totalEquity' => $balanceSheetStatement->getTotalEquity(),
                'totalLiabilitiesAndStockholdersEquity' => $balanceSheetStatement->getTotalLiabilitiesAndStockholdersEquity(),
                'minorityInterest' => $balanceSheetStatement->getMinorityInterest(),
                'totalLiabilitiesAndTotalEquity' => $balanceSheetStatement->getTotalLiabilitiesAndTotalEquity(),
                'totalInvestments' => $balanceSheetStatement->getTotalInvestments(),
                'totalDebt' => $balanceSheetStatement->getTotalDebt(),
                'netDebt' => $balanceSheetStatement->getNetDebt(),
                'link' => $balanceSheetStatement->getLink(),
                'finalLink' => $balanceSheetStatement->getFinalLink(),
            ];

            if($balanceSheetStatementDAO->exists($idStock, $balanceSheetStatement->getCalendarYear(), $balanceSheetStatement->getPeriod())) {
                $filter_rules = [
                    [$idStock, 'equal', 'idStock'],
                    ['calendarYear', 'equal', $balanceSheetStatement->getCalendarYear()],
                    ['timePeriod', 'equal', $balanceSheetStatement->getPeriod()]
                ];
                $idBalanceSheetStatement = $balanceSheetStatementDAO->setColumns('idBalanceSheetStatement')->getMultiple(filter: $filter_rules)->getValueAsInt('idBalanceSheetStatement');
                $balanceSheetStatementData['idBalanceSheetStatement'] = $idBalanceSheetStatement;
                $recordSet = $balanceSheetStatementDAO->update($balanceSheetStatementData);
            }
            else {
                $recordSet = $balanceSheetStatementDAO->insert($balanceSheetStatementData);
            }
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    };

    $importBalanceSheetStatement($balanceSheetStatementAnnual, $idStock, $symbol);
    $importBalanceSheetStatement($balanceSheetStatementQuarterly, $idStock, $symbol);
}

function cashflowStatementImporter(FmpApiClient $client, string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $cashflowStatementAnnual = $client->getCashflowStatement($symbol);
    echo 'CashflowStatement for: '.$symbol.LINE_BREAK;
    $cashflowStatementAnnual->dump();

    $cashflowStatementQuarterly = $client->getCashflowStatement($symbol, \pofolio\classes\FMP\Response\CashflowStatement::PERIOD_QUARTER);
    // dump not necessary

    $importCashflowStatement = static function(
        \pofolio\classes\FMP\Response\CashflowStatement $cashflowStatement,
        mixed $idStock,
        string $symbol
    ): void {
        $cashflowStatementDAO = CashflowStatement::create();
        $currencyDAO = Currency::create();
        foreach($cashflowStatement as $ignored) {

            $idReportedCurrency = $currencyDAO->setColumns('idCurrency')->get($cashflowStatement->getReportedCurrency(),
                'currency')->getValueAsInt('idCurrency') ?:
                Stock::create()->setColumns('idCurrency')->get($idStock, 'idStock')->getValueAsInt('idCurrency');

            $periodOrder = match ($cashflowStatement->getPeriod()) {
                CashflowStatement::PERIOD_QUARTER1 => 1,
                CashflowStatement::PERIOD_QUARTER2 => 2,
                CashflowStatement::PERIOD_QUARTER3 => 3,
                CashflowStatement::PERIOD_QUARTER4 => 4,
                CashflowStatement::PERIOD_FISCAL_YEAR => 0,
                default => throw new RuntimeException('Unknown period: ' . $cashflowStatement->getPeriod()),
            };

            $calendarYear = $cashflowStatement->getCalendarYear();
            $period = $cashflowStatement->getPeriod();

            $cashflowStatementData = [
                'idStock' => $idStock,
                'date' => $cashflowStatement->getDate(),
                'idReportedCurrency' => $idReportedCurrency,
                'CIK' => $cashflowStatement->getCIKAsInt(),
                'fillingDate' => $cashflowStatement->getFillingDate(),
                'acceptedDate' => $cashflowStatement->getAcceptedDate(),
                'calendarYear' => $calendarYear,
                'timePeriod' => $period,
                'periodOrder' => $periodOrder,
                'netIncome' => $cashflowStatement->getNetIncome(),
                'depreciationAndAmortization' => $cashflowStatement->getDepreciationAndAmortization(),
                'deferredIncomeTax' => $cashflowStatement->getDeferredIncomeTax(),
                'stockBasedCompensation' => $cashflowStatement->getStockBasedCompensation(),
                'changeInWorkingCapital' => $cashflowStatement->getChangeInWorkingCapital(),
                'accountsReceivables' => $cashflowStatement->getAccountsReceivables(),
                'inventory' => $cashflowStatement->getInventory(),
                'accountsPayables' => $cashflowStatement->getAccountsPayables(),
                'otherWorkingCapital' => $cashflowStatement->getOtherWorkingCapital(),
                'otherNonCashItems' => $cashflowStatement->getOtherNonCashItems(),
                'netCashProvidedByOperatingActivities' => $cashflowStatement->getNetCashProvidedByOperatingActivities(),
                'investmentsInPropertyPlantAndEquipment' => $cashflowStatement->getInvestmentsInPropertyPlantAndEquipment(),
                'acquisitionsNet' => $cashflowStatement->getAcquisitionsNet(),
                'purchasesOfInvestments' => $cashflowStatement->getPurchasesOfInvestments(),
                'salesMaturitiesOfInvestments' => $cashflowStatement->getSalesMaturitiesOfInvestments(),
                'otherInvestingActivites' => $cashflowStatement->getOtherInvestingActivites(),
                'netCashUsedForInvestingActivites' => $cashflowStatement->getNetCashUsedForInvestingActivites(),
                'debtRepayment' => $cashflowStatement->getDebtRepayment(),
                'commonStockIssued' => $cashflowStatement->getCommonStockIssued(),
                'commonStockRepurchased' => $cashflowStatement->getCommonStockRepurchased(),
                'dividendsPaid' => $cashflowStatement->getDividendsPaid(),
                'otherFinancingActivites' => $cashflowStatement->getOtherFinancingActivites(),
                'netCashUsedProvidedByFinancingActivities' => $cashflowStatement->getNetCashUsedProvidedByFinancingActivities(),
                'effectOfForexChangesOnCash' => $cashflowStatement->getEffectOfForexChangesOnCash(),
                'netChangeInCash' => $cashflowStatement->getNetChangeInCash(),
                'cashAtEndOfPeriod' => $cashflowStatement->getCashAtEndOfPeriod(),
                'cashAtBeginningOfPeriod' => $cashflowStatement->getCashAtBeginningOfPeriod(),
                'operatingCashFlow' => $cashflowStatement->getOperatingCashFlow(),
                'capitalExpenditure' => $cashflowStatement->getCapitalExpenditure(),
                'freeCashFlow' => $cashflowStatement->getFreeCashFlow(),
                'link' => $cashflowStatement->getLink(),
                'finalLink' => $cashflowStatement->getFinalLink(),
            ];

            if($cashflowStatementDAO->exists($idStock, $cashflowStatement->getCalendarYear(), $cashflowStatement->getPeriod())) {
                $filter_rules = [
                    [$idStock, 'equal', 'idStock'],
                    ['calendarYear', 'equal', $cashflowStatement->getCalendarYear()],
                    ['timePeriod', 'equal', $cashflowStatement->getPeriod()]
                ];
                $idCashflowStatement = $cashflowStatementDAO->setColumns('idCashflowStatement')->getMultiple(filter: $filter_rules)->getValueAsInt('idCashflowStatement');
                $cashflowStatementData['idCashflowStatement'] = $idCashflowStatement;
                $recordSet = $cashflowStatementDAO->update($cashflowStatementData);
            }
            else {
                $recordSet = $cashflowStatementDAO->insert($cashflowStatementData);
            }
            if($lastError = $recordSet->getLastError()) {
                throw new RuntimeException($lastError['message'], $lastError['code']);
            }
        }
    };

    $importCashflowStatement($cashflowStatementAnnual, $idStock, $symbol);
    $importCashflowStatement($cashflowStatementQuarterly, $idStock, $symbol);
}

function refreshShareFloat(int|string $symbol): void
{
    [$idStock, $symbol] = extractSymbol($symbol);
    $shareFloatDAO = ShareFloat::create();
    $recordSet = $shareFloatDAO->getMultiple(filter: [['idStock', 'equal', $idStock]], sorting: ['date' => 'DESC'], limit: [1]);
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