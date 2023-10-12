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
    $countrySet = $text[$i];

    // Kommentarzeilen überspringen
    if(str_starts_with($countrySet, '#')) {
        if(str_starts_with($countrySet, '#ISO')) {
            $keys = explode("\t", trim($countrySet));
            $keys[0] = substr($keys[0], 1);
        }
        continue;
    }

    // zwischen tabulator aufspalten
    $countrySet = explode("\t", $countrySet);
    $row = [];
    for($j = 0, $jMax = count($countrySet); $j < $jMax; $j++) {
        $row[trim($keys[$j])] = trim($countrySet[$j]);
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
        $countryData['idLanguage'] = $languageSet->getValueAsInt('idLanguage');
    }
    unset($languageSet);

    if(!($CountryDAO->exists($iso3316_alpha2))) {
        // insert
        $CountryDAO->insert($countryData);
    }
    else {
        // update
        $CountryDAO->setColumns(
            'Country.idCountry',
            'Country.blockUpdate'
        );
        $filter = [
            ['Country.isoCode', 'equal', $iso3316_alpha2]
        ];

        $countrySet = $CountryDAO->getMultiple(filter_rules: $filter);
        if(count($countrySet)) {
            if($countrySet->getValueAsBool('blockUpdate')) {
                continue;
            }
            $countryData['idCountry'] = $countrySet->getValueAsInt('idCountry');
            $CountryDAO->update($countryData);
        }
    }
}

// Import locales/languages from intl extension

$locales = resourcebundle_locales('');

foreach($locales as $locale) {
    if(!str_contains($locale, '_')) {
        continue;
    }

    $primaryLanguage = locale_get_primary_language($locale);
    $displayLanguage = locale_get_display_language($locale);
    $region = locale_get_region($locale);

    $localeData = [
        'locale' => $locale
    ];

    // check whether language is included
    $languageFilter = [
        ['Language.code', 'equal', $primaryLanguage]
    ];
    $languageSet = $LanguageDAO->getMultiple(filter_rules: $languageFilter);
    if(count($languageSet)) {
        // update
        $idLanguage = $languageSet->getValueAsInt('idLanguage');
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
    $localeData['idLanguage'] = $idLanguage;

    $countryFilter = [
        ['Country.isoCode', 'equal', $region]
    ];

    // determine country
    $countrySet = $CountryDAO->getMultiple(filter_rules: $countryFilter);
    if(count($countrySet)) {
        $localeData['idCountry'] = $countrySet->getValueAsInt('idCountry');
    }

    // check if locale exists
    $localeFilter = [
        ['Locale.locale', 'equal', $locale]
    ];
    $locales_exists = $LocaleDAO->getCount(filter_rules: $localeFilter)->getValueAsBool('count');

    if(!$locales_exists) {
        $LocaleDAO->insert($localeData);
    }
    else {
        $LocaleDAO->setColumns(
            'Locale.idLocale',
        );
        $idLocales = $LocaleDAO->getMultiple(filter_rules: $localeFilter)->getValueAsInt('idLocale');
        $localeData['idLocale'] = $idLocales;

        $LocaleDAO->update($localeData);
    }
}