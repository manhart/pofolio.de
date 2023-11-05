class GUI_Watchlist extends GUI_Module
{
    #interval = 5000;
    #timeoutID;
    init(options = {})
    {
        const ajaxURL = new Url();
        ajaxURL.setParam('module', this.fullyQualifiedClassName);
        ajaxURL.setParam('method', 'getWatchlist');

        let watchlistTable = new Tabulator("div.watchlist", {
            index: 'idStock',
            height: '840px',
            headerFilterLiveFilterDelay: 500,
            // layout: 'fitColumns',
            placeholder: function(){
                return this.getHeaderFilters().length ? 'No Matching Stocks' : 'No Stocks in Watchlist'; //set placeholder based on if there are currently any header filters
            },
            columns: [
                {title: 'ID', field: 'idStock', headerSortTristate: true, width: 70},
                {title: 'Symbol', field: 'symbol', headerSortTristate: true, width: 150, headerFilter: 'input', headerFilterPlaceholder: 'Symbol'},
                {title: 'Name', field: 'name', headerSortTristate: true, headerFilter: 'input', headerFilterPlaceholder: 'Name of stock'},
                {title: 'ISIN', field: 'ISIN', headerSortTristate: true, width: 150, headerFilter: 'input', headerFilterPlaceholder: 'ISIN'},
                {title: 'MCap', field: 'marketCap', headerSortTristate: true, hozAlign: 'right', headerFilter: 'input', headerFilterPlaceholder: 'Marktkapitalisierung'},
                // {title: 'Änderung', field: 'change', headerSortTristate: true, hozAlign: 'right', headerFilter: 'input', headerFilterPlaceholder: 'Änderung',
                //     formatter: 'money', formatterParams: {precision: 2, thousand: '.', decimal: ','}},
                {title: 'Kurs', field: 'price', headerSortTristate: true, hozAlign: 'right', headerFilter: 'input', headerFilterPlaceholder: 'Preis',
                    formatter: 'money', formatterParams: {precision: 2, thousand: '.', decimal: ','}},
                {title: '%', field: 'changePercent', headerSortTristate: true, hozAlign: 'right', headerFilter: 'input', headerFilterPlaceholder: '%'},

            ],
            sortMode: 'remote',
            filterMode: 'remote',
            ajaxURL: ajaxURL.getUrl(),
            ajaxConfig: {
                method: 'POST',
                // headers: {
                //     'X-Requested-With': 'XMLHttpRequest'
                // }
            },
            ajaxResponse:function(url, params, response){
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.

                return response; //return the tableData property of a response json object
            },
            progressiveLoad: 'scroll'
        });

        this.#timeoutID = setTimeout(this.getQuotes.bind(this, watchlistTable), 1000);
    }

    getQuotes = async (table) =>
    {
        clearTimeout(this.#timeoutID);

        const stockIds = table.getRows().map(function(row) {
            return row.getData().idStock;
        });

        const quotes = await this.request('getQuotes', {stockIds}, {method: 'POST'});
        if(quotes.length) {
           table.updateData(quotes);
        }
        // this.#timeoutID = setTimeout(this.getQuotes.bind(this, table), this.#interval);
    }
}
Weblication.registerClass(GUI_Watchlist);