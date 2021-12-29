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
