<?php
/*
 * This file is part of the pofolio.de project.
 *
 * @copyright Copyright (c) 2023. Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * GUI_Watchlist.php created on 29.10.23, 12:29.
 *
 */

namespace pofolio\guis\GUI_WatchList;

use pofolio\classes\FMP\Client\FmpApiClient;
use pofolio\dao\mysql\pofolio\Stock;
use pool\classes\Core\Input\Input;

/**
 * Class GUI_Watchlist
 * @package pofolio
 */
class GUI_Watchlist extends \GUI_Module
{
    /**
     * @var array<string, string> $templates files (templates) to be loaded, usually used with $this->Template->setVar(...) in the prepare function. Defined as an associated array [handle => tplFile].
     */
    protected array $templates = [
        'stdout' => 'tpl_watchlist.html',
    ];

    /**
     * @var int defines which superglobals should be used in this module. Superglobal variables are passed to superglobals in the Input class.
     */
    protected int $superglobals = Input::POST;

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('getWatchlist', $this->getWatchlist(...), true);
        $this->registerAjaxMethod('getQuotes', $this->getQuotes(...));
    }

    protected function getWatchlist(int $page = 1, int $size = 5, array $sort = [], array $filter = []): array
    {
        $stockDAO = Stock::create();

        $stockDAO->setColumns('idStock', 'symbol', 'name', 'ISIN', 'marketCap', 'price', 'changePercent');
        $stockDAO->setFormatter('marketCap', $this->formatMarketCap(...));
        $stockDAO->setFormatter('price', $this->formatPrice(...));
        $stockDAO->setFormatter('changePercent', $this->formatChangePercent(...));

        $limit = [
            ($page - 1) * $size,
            $size,
        ];

        $sorting = [];
        if($sort) {
            foreach($sort as $item) {
                $sorting[$item['field']] = $item['dir'];
            }
        }
        $filterRules = [];
        if($filter) {
            foreach($filter as $item) {
                $filterRules[] = [$item['field'], $item['type'], "%{$item['value']}%"];
            }
        }
        $stockSet = $stockDAO->getMultiple(filter: $filterRules, sorting: $sorting, limit: $limit, options: ['SQL_CALC_FOUND_ROWS']);

        // calc last page
        $amountOfRows = $stockDAO->foundRows();
        $lastPage = ceil($amountOfRows / $size);

        return [
            'data' => $stockSet->getRaw(),
            'last_page' => $lastPage
        ];
    }

    protected function formatMarketCap(float $marketCap, array $row = []): string
    {
        return abbreviateNumber($marketCap);
    }

    protected function formatPrice(float $price, array $row = []): string
    {
        return number_format($price, 2, '.', '');
    }

    protected function formatChangePercent(float $changePercent, array $row = []): string
    {
        $sign = ($changePercent > 0) ? '+' : '';
        return $sign.number_format($changePercent, 2, '.', '.') . '%';
    }

    protected function getQuotes(array $stockIds): array
    {
        if(!$stockIds) {
            return [];
        }
        $stockDAO = Stock::create();
        $stockDAO->setColumns('idStock', 'symbol', 'previousClose');
        $stockSet = $stockDAO->getMultiple(filter: [['idStock', 'in', $stockIds]]);

        $quoteResponse = FmpApiClient::getInstance()->getQuote($stockSet->getFieldData('symbol'));

        $quotes = [];
        foreach($quoteResponse as $ignore) {
            $symbol = $quoteResponse->getSymbol();
            $price = $quoteResponse->getPrice();
            $volume = $quoteResponse->getVolume();
            $lastOutstandingShares = $quoteResponse->getSharesOutstanding();
            if($stockSet->find('symbol', $symbol) !== false)
                $previousClose = $stockSet->getValueAsFloat('previousClose');
            else
                $previousClose = $quoteResponse->getPreviousClose(); // sometimes with data errors

            $changePercent = round(($price / $previousClose) * 100 - 100, 2);
            $idStock = $stockDAO->get($symbol, 'symbol')->getValueAsInt('idStock');

            $marketCap = round($price * $lastOutstandingShares);
            $quote = [
                'idStock' => $idStock,
                'price' => $price,
                'volume' => $volume,
                'lastOutstandingShares' => $lastOutstandingShares,
                'marketCap' => $marketCap,
                'previousClose' => $previousClose,
            ];
            $stockDAO->update($quote);
            $quote['marketCap'] = $this->formatMarketCap($marketCap);
            $quote['price'] = $this->formatPrice($price);
            $quote['changePercent'] = $this->formatChangePercent($changePercent);
            $quotes[] = $quote;
        }

        return $quotes;
    }
}