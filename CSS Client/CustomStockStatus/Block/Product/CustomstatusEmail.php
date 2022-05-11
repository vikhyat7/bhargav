<?php

namespace Mageants\CustomStockStatus\Block\Product;

class CustomstatusEmail extends \Magento\Framework\View\Element\Template
{
	private $product;

	public function setProduct($product)
	{
	    $this->product = $product;
	}
	
	public function getProduct()
    {
      return $this->product;  
    }
}
