<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Plugin;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class AttributePlugin
{
    public $request;

    public $customIconManage;

    public $iconHelper;

    public $filesystem;

    public $customRuleManage;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Mageants\CustomStockStatus\Model\CustomStockStFactory $customIconManage,
        \Mageants\CustomStockStatus\Model\CustomStockRuleFactory $customRuleManage,
        \Mageants\CustomStockStatus\Helper\Data $iconHelper,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->request = $request;
        $this->customIconManage = $customIconManage;
        $this->customRuleManage = $customRuleManage;
        $this->iconHelper = $iconHelper;
        $this->filesystem = $filesystem;
    }

    public function aroundSave(Attribute $subject, \Closure $proceed)
    {
        $subject = $subject;
        $postData = $this->request->getPostValue();
        
        // echo "<pre>";
        // var_dump($postData);
        // exit();
        $imageFiles = $this->request->getFiles('manage_icon');
        
        if (isset($postData['manage_icon_delete'])) {
            $mediaDirectory = $this->filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );

            foreach ($postData['manage_icon_delete'] as $optionId => $deleteImage) {
                if ($deleteImage == 1) {
                    $customIconDelete = $this->customIconManage->create();
                    $optionIconDeleteCollection = $customIconDelete->getCollection()
                    ->addFieldToFilter('option_id', $optionId)->getFirstItem();

                    if (!empty($optionIconDeleteCollection->getData())) {
                        unlink($mediaDirectory->getAbsolutePath($optionIconDeleteCollection->getData('icon')));
                        $customIconDelete->load($optionIconDeleteCollection->getData('id'));
                        $customIconDelete->delete();
                    }
                }
            }
        }

        if (isset($imageFiles)) {
            foreach ($imageFiles as $optionId => $icon) {
                if ($icon['name']) {
                    $optionIcon = $this->iconHelper->iconUpload($icon['name'], $optionId);
                    $customIcon = $this->customIconManage->create();
                    $optionIconCollection = $customIcon->getCollection()
                    ->addFieldToFilter('option_id', $optionId)->getFirstItem();
                             
                    if (!empty($optionIconCollection->getData())) {
                        $customIcon->load($optionIconCollection->getData('id'));
                    }

                    $customIcon->setOptionId($optionId);
                    $customIcon->setIcon($optionIcon);
                    $customIcon->save();
                }
            }
        }
       
        if (isset($postData['custom_status_rule_range'])) {
            foreach ($postData['custom_status_rule_range'] as $rangeRule) {
                
                $customRule = $this->customRuleManage->create();
                if ($rangeRule['delete']) 
                {
                    $customRule->load($rangeRule['delete']);
                    $customRule->delete();
                    continue;
                }
                elseif (isset($rangeRule['id'])) {
                    if($rangeRule['id'])
                    {
                        $customRule->load($rangeRule['id']);
                    }
                }

                if (isset($rangeRule['from']) && isset($rangeRule['to']) && isset($rangeRule['option_id']) && isset($rangeRule['rule_id'])) {
                    $customRule->setFrom($rangeRule['from']);
                    $customRule->setTo($rangeRule['to']);
                    $customRule->setOptionId($rangeRule['option_id']);
                    $customRule->setRuleId($rangeRule['rule_id']);
                    $customRule->save();
                }
            }
        }
        return $proceed();
    }
}
