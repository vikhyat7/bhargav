/*var config = {
 config: {
    mixins: {
        'Magento_Theme/js/view/messages': {
            'Mageants_SpamAndBotBlocker/js/view/messages': true
            }
        }
    }
};*/

var config = {
    map: {
        '*': {
            'Magento_Theme/js/view/messages':'Mageants_OutofStockNotification/js/view/messages'
        }
    }
};