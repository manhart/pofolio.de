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
    }

    protected function getWatchlist(int $page = 1, int $size = 5, array $sort = [], array $filter = []): array
    {
        $stockDAO = Stock::create();
        $stockDAO->setColumns('idStock', 'symbol', 'name', 'ISIN');
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
        $stockSet = $stockDAO->getMultiple(filter_rules: $filterRules, sorting: $sorting, limit: $limit, options: ['SQL_CALC_FOUND_ROWS']);

        // calc last page
        $amountOfRows = $stockDAO->foundRows();
        $lastPage = ceil($amountOfRows / $size);

        return [
            'data' => $stockSet->getRaw(),
            'last_page' => $lastPage
        ];
    }
}