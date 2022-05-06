var config = {
    config: {
        map: {
            '*': {
                'mageants_stockstatus': "Mageants_CustomStockStatus/js/mageantsstockstatus",
            }
        },
        mixins: {
            'mage/SwatchRenderer': {
                'Magento_Swatches/js/swatch-renderer' : true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Mageants_CustomStockStatus/js/swatch-renderer' : true
            }
        }    
    }
};
