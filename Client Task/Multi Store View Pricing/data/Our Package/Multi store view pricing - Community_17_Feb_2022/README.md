# Multi store view pricing

	Current version number: 2.0.7
	Date :- 24/11/2021
	************************************************************
 	Bug fixed
	************************************************************
	- When admin open product in backend then give error in Magento2.4.3 version, MageAnts team resolved issue and make compatible with Magento2.4.3 version.

	************************************************************
 	Bug fixed
	************************************************************
	Mageants/StoreViewPricing/etc/module.xml
	Mageants/StoreViewPricing/Composer.json
	Mageants/StoreViewPricing/Ui/Component/Listing/Columns/Column/Price.php

=====================================================================================================================================================

	Current version number: 2.0.6
	Date :- 03/08/2021
	************************************************************
 	Bug fixed
	************************************************************
	- MageAnts team resolved issue of tier price update using csv file in magento242 version, Extension working fine in all magento version.

	************************************************************
 	Bug fixed
	************************************************************
	Mageants/StoreViewPricing/etc/module.xml
	Mageants/StoreViewPricing/Composer.json
	Mageants/StoreViewPricing/Model/Import.php
	Mageants/StoreViewPricing/Observer/Productview.php
	Mageants/StoreViewPricing/Setup/InstallSchema.php

=====================================================================================================================================================

	Current version number: 2.0.5
	Date :- 27/08/2020
	************************************************************
 	Bug fixed
	************************************************************
	- MageAnts team make a Multi store view pricing magento 2 extension compatible in New Magento2.4 version.
	- Make a only one sample csv file to update price in all magento version using csv file, Extension working fine in all Magento version.

	************************************************************
 	Bug fixed
	************************************************************
	Mageants/StoreViewPricing/etc/module.xml
	Mageants/StoreViewPricing/Composer.json

	************************************************************
 	New added files
	************************************************************
	Mageants/StoreViewPricing/Plugin/Initialization.php

=====================================================================================================================================================


	Current version number: 2.0.4
	Date :- 17/04/2019
	************************************************************
 	Bug fixed
	************************************************************
	- If admin update store view price using Csv file then Csv file work with SKU and update on particular SKU but admin have multiple store view with same Sku then it's update in particular sku with last store not in all store, this issue in magento2.3 now solve issue and working fine in magento2.3.

	************************************************************
 	Bug fixed
	************************************************************
	Mageants/StoreViewPricing/etc/module.xml
	Mageants/StoreViewPricing/Composer.json
	Mageants/StoreViewPricing/Model/Import.php

	************************************************************
 	New added files
	************************************************************
	Mageants/StoreViewPricing/etc/adminhtml/di.xml
	Mageants/StoreViewPricing/Model/Importmageants.php (overrided)
	Mageants/StoreViewPricing/Model/Import/AbstractEntity.php

=====================================================================================================================================================

	Current version number: 2.0.3
	Date :- 11/03/2019
	************************************************************
 	Bug fixed
	************************************************************
	- MageAnts update Multi store view pricing extension in latest magento2.3 version, Now extension working with all magento version.

=====================================================================================================================================================

	Current version number: 2.0.3
	Date :- 07/08/2018
	************************************************************
 	Bug fixed
	************************************************************
	- Update module version name in composer.json file same as module.xml file.

=====================================================================================================================================================


	Current version number: 2.0.3
	Date :- 07/08/2018
	************************************************************
    Bug fixed
	************************************************************
	- In backend when user edit any product and add releted product then entity_id not set and give error
	  so now issue fixed and working fine in all magento version also update module version number.
	  
	
	************************************************************
	 File list
	************************************************************
	Mageants/StoreViewPricing/Ui/Component/Listing/Columns/Column/Price.php
	Mageants/StoreViewPricing/etc/module.xml

=====================================================================================================================================================
	Current version number: 2.0.2
	Date :- 10/07/2018
	************************************************************
    Bug fixed
	************************************************************
	- if i have 3 store view like india, Japan, americian and i was change store view price for india then it's also change for japan and americian 
	  it's happen in backend as well as frontend, so now this issue fixed.
	  
	- if i have 3 store view like india, Japan, americian and i was change store view price for india then it's also change for japan and americian 
	  it's happen in product list page and product view page, so now this issue fixed.
	  
	- user has default store view in product list page in admin side then user filter like " Store ='Japan' " then filter properly working and 
	  change store='japan' and also change store view price but when user clear this filter then user redirect to default store view but user 
	  see japan store view price. so now this issue fixed.
	
	************************************************************
	File list
	************************************************************
	Mageants\StoreViewPricing\Observer\Productview.php
	Mageants\StoreViewPricing\Observer\Productprice.php
	Mageants\StoreViewPricing\Plugin\Price.php
	Mageants\StoreViewPricing\etc\di.xml
	Mageants\StoreViewPricing\Ui\Component\Listing\Columns\Column\Price.php
	Mageants\StoreViewPricing\etc\module.xml
