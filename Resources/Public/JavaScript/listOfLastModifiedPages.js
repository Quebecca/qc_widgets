function selectNumberOfItems(){
    var select = document.getElementById('numberOfItems');
    var value = select.options[select.selectedIndex].value;
    require(['TYPO3/CMS/Core/Ajax/AjaxRequest'], function (AjaxRequest) {
        new AjaxRequest(TYPO3.settings.ajaxUrls.set_number_of_items)
            .withQueryArguments({numberOfItems: value})
            .get()
            .then(async function (response) {
               console.log('set the number of items')
            });
    });
}
// https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/JavaScript/Templating/Index.html
// https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/JavaScript/EventApi/Index.html
