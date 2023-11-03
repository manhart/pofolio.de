class GUI_Watchlist extends GUI_Module
{
    init(options = {})
    {
        const ajaxURL = new Url();
        ajaxURL.setParam('module', this.fullyQualifiedClassName);
        ajaxURL.setParam('method', 'getWatchlist');

        let watchlistTable = new Tabulator("div.watchlist", {
            height: '540px',
            headerFilterLiveFilterDelay: 500,
            // layout: 'fitColumns',
            placeholder: 'No stocks in watchlist',
            columns: [
                {title: 'ID', field: 'idStock', headerSortTristate: true, width: 70},
                {title: 'Symbol', field: 'symbol', headerSortTristate: true, width: 150, headerFilter: 'input', headerFilterPlaceholder: 'Symbol'},
                {title: 'Name', field: 'name', headerSortTristate: true, headerFilter: 'input', headerFilterPlaceholder: 'Name of stock'},
                {title: 'ISIN', field: 'ISIN', headerSortTristate: true, width: 150, headerFilter: 'input', headerFilterPlaceholder: 'ISIN'},
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
        // watchlistTable.setData();
    }
}
Weblication.registerClass(GUI_Watchlist);