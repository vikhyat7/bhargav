<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider;

/**
 * Class CompletedDataProvider
 * @package Magestore\OrderSuccess\Ui\DataProvider
 */
class CompletedDataProvider extends \Magestore\OrderSuccess\Ui\DataProvider\OrderDataProvider
{
    /**
     * {@inheritdoc}
     */
    public function getOrderCollection()
    {
        /** @var Collection $collection */
        $collection= $this->context->getCompletedCollectionFactory()->create();
        return $collection;
    }
    
}
