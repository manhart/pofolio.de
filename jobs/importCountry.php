<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 */

namespace pofolio\jobs;

// PHP gets an incredible amount of time
use pofolio\dao\mysql\pofolio\Country;
use pofolio\dao\mysql\pofolio\Language;
use pofolio\dao\mysql\pofolio\Locale;
use pool\classes\Database\DataInterface;
use pool\classes\Database\Driver\MySQLi;

\set_time_limit(0);

// the implicit flush is turned on, so you can see immediately the output
\ob_implicit_flush(1);

// get my process id
$pid = \getmypid();

if(!$pid) {
    die('Process ID couldn\'t be detected.');
}

// is console
const IS_CLI = PHP_SAPI === 'cli';
if(IS_CLI) {
    $_SERVER['DOCUMENT_ROOT'] = '/virtualweb/manhart';
    if(!\is_dir($_SERVER['DOCUMENT_ROOT'])) {
        die('Root directory ' . $_SERVER['DOCUMENT_ROOT'] . ' is missing!');
    }
    $_SERVER['SERVER_NAME'] = gethostbyaddr('127.0.1.1'); // php_uname('n');
    $lineBreak = \chr(10);
}
else {
    $lineBreak = '<br>';
    $options = $_REQUEST;
}
define('LINE_BREAK', $lineBreak);


// include libs: POOL
const DIR_CONFIGS_ROOT = '/virtualweb/manhart/pofolio/config';
require_once DIR_CONFIGS_ROOT . '/config.inc.php'; // <-- innerhalb config.inc.php die Pfade anpassen!

// POOL integration
require_once DIR_POOL_ROOT . '/pool.lib.php';

// global composer packages
// require DIR_DOCUMENT_ROOT.'/g7portal/vendor/autoload.php';
define('JOB_NAME', remove_extension(basename(__FILE__)));


$connectOptions = [
    'host' => MYSQL_HOST,
    'database' => [\DB_POFOLIO],
];
DataInterface::createDataInterface($connectOptions, MySQLi::getInstance());

$CountryDAO = Country::create();
$LanguageDAO = Language::create();
$LocaleDAO = Locale::create();

// Textdatei zeilenweise einlesen
$dateiname = 'http://download.geonames.org/export/dump/countryInfo.txt';
$text = \file($dateiname);

$keys = [];
$count = count($text);
for($i = 0; $i < $count; $i++) {
    $locale = '';
    $countryRow = $text[$i];

    // Kommentarzeilen überspringen
    if(str_starts_with($countryRow, '#')) {
        if(str_starts_with($countryRow, '#ISO')) {
            $keys = explode("\t", trim($countryRow));
            $keys[0] = substr($keys[0], 1);
        }
        continue;
    }

    // zwischen tabulator aufspalten
    $countryRow = explode("\t", $countryRow);
    $row = [];
    for($j = 0, $jMax = count($countryRow); $j < $jMax; $j++) {
        $row[trim($keys[$j])] = trim($countryRow[$j]);
    }

    $iso3316_alpha2 = $row['ISO'];
    $country = $row['Country'];
    $capital = $row['Capital'];
    $continent = $row['Continent'];
    $tld = $row['tld'];
    $currencyCode = $row['CurrencyCode'];
    $currencyName = $row['CurrencyName'];
    $phonePrefix = $row['Phone'];
    $postalCodeFormat = $row['Postal Code Format'];
    $postalCodeRegex = $row['Postal Code Regex'];
    $languages = $row['Languages'];

    // mainlanguage und locale auslesen
    $mainLanguage = explode(",", $languages)[0];
    if(str_contains($mainLanguage, '-')) {
        $mainLanguage = substr($mainLanguage, 0, 2);
    }
    if(!isEmptyString($mainLanguage)) {
        $locale = $mainLanguage . '_' . $iso3316_alpha2;
    }

    $countryData = [
        'isoCode' => $iso3316_alpha2,
        'countryName' => $country,
        'capital' => $capital,
        'continent' => $continent,
        'tld' => $tld,
        'currencyCode' => $currencyCode,
        'currencyName' => $currencyName,
        'phonePrefix' => $phonePrefix,
        'postalCodeFormat' => $postalCodeFormat,
        'postalCodeRegex' => $postalCodeRegex,
        'languages' => $languages,
        'locale' => $locale
    ];

    // idLanguage ermitteln, wenn sie existiert
    $LanguageDAO->setColumns(
        'Language.idLanguage',
    );
    $languageSet = $LanguageDAO->getMultiple(filter_rules: [['Language.code', 'equal', $mainLanguage]]);
    if(count($languageSet)) {
        $languageRow = $languageSet->getRaw();
        $idLanguage = $languageRow[0]['idLanguage'];
        $countryData['idLanguage'] = $idLanguage;
    }

    // überprüfen, obe es ein Insert oder ein Update ist
    $filter = [
        ['Country.isoCode', 'equal', $iso3316_alpha2]
    ];
    $country_exists = $CountryDAO->getCount(filter_rules: $filter)->getValueAsBool('count');

    if(!$country_exists) {
        // insert
        $CountryDAO->insert($countryData);
    }
    else {
        // update
        $CountryDAO->setColumns(
            'Country.idCountry',
            'Country.blockUpdate'
        );
        $countryRow = $CountryDAO->getMultiple(filter_rules: $filter)->getRaw();
        if(count($countryRow)) {
            $blockUpdate = $countryRow['0']['blockUpdate'];
            if($blockUpdate) {
                continue;
            }
            $idCountry = $countryRow['0']['idCountry'];
            $countryData['idCountry'] = $idCountry;
            $CountryDAO->update($countryData);
        }
    }
}

// locales/sprachen aus intl extension importieren

$locales = resourcebundle_locales('');

foreach($locales as $locale) {
    if(!str_contains($locale, '_')) {
        continue;
    }

    $primaryLanguage = locale_get_primary_language($locale);
    $displayLanguage = locale_get_display_language($locale);
    $region = locale_get_region($locale);

    $localesData = [
        'locales' => $locale
    ];

    // überprüfen, ob sprache enthalten ist
    $languageFilter = [
        ['Language.code', 'equal', $primaryLanguage]
    ];
    $languageRow = $LanguageDAO->getMultiple(null, null, $languageFilter)->getRaw();
    if($languageRow) {
        // update
        $idLanguage = $languageRow[0]['idLanguage'];

        $languageData = [
            'idLanguage' => $idLanguage,
            'code' => $primaryLanguage,
            'language' => $displayLanguage,
        ];

        $LanguageDAO->update($languageData);
    }
    else {
        // insert
        $languageData = [
            'code' => $primaryLanguage,
            'language' => $displayLanguage,
        ];
        $Set = $LanguageDAO->insert($languageData);
        $idLanguage = $Set->getValue('last_insert_id');
    }
    $localesData['idLanguage'] = $idLanguage;

    $countryFilter = [
        ['Country.isoCode', 'equal', $region]
    ];

    // Land ermitteln
    $CountrySet = $CountryDAO->getMultiple(filter_rules: $countryFilter)->getRaw();
    if($CountrySet) {
        $idCountry = $CountrySet[0]['idCountry'];
        $localesData['idCountry'] = $idCountry;
    }

    // überprüfen, obe es ein Insert oder ein Update ist
    $localesFilter = [
        ['Locales.locales', 'equal', $locale]
    ];
    $locales_exists = $LocaleDAO->getCount(filter_rules: $localesFilter)->getValueAsBool('count');

    if(!$locales_exists) {
        $LocaleDAO->insert($localesData);
    }
    else {
        $LocaleDAO->setColumns(
            'Locales.idLocales',
        );
        $idLocales = $LocaleDAO->getMultiple(filter_rules: $localesFilter)->getRaw()[0]['idLocales'];
        $localesData['idLocales'] = $idLocales;

        $LocaleDAO->update($localesData);
    }
}