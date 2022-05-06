<?php


namespace Magestore\Webpos\Test\Api\ScanProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;
use Magento\Mtf\Fixture\FixtureInterface;
use Magestore\Webpos\Test\Constant\Product;
/**
 * Trait SearchProductsTrait
 * @package Magestore\Webpos\Test\Api\SearchProducts
 */
trait ScanProductsTrait
{

    /**
     * @var $requestData
     */
    protected $requestData;

    /**
     * @var
     */
    protected $queryString;

    /**
     * @param null $queryString
     * @param null $pageSize
     * @param null $currentPage
     * @return array
     */
    public function createRequestData($queryString = null , $pageSize = null , $currentPage = null){
        $this->queryString = $queryString;
        $requestData = [
            'searchCriteria' => [
                'queryString' => $queryString ? $queryString : '',
                SearchCriteria::PAGE_SIZE => $pageSize ? $pageSize : 100,
                SearchCriteria::CURRENT_PAGE => $currentPage ? $currentPage : 1
            ],
        ];
        return $this->requestData = $requestData;
    }

    /**
     * @param $response
     */
    public function expectedEmptyData($response){
        $expectedTotalCount = 0;

        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $this->assertNotNull($response, $message);

        /* check search_criteria */
        unset($this->requestData['searchCriteria']['queryString']);
        AssertArrayContains::assert($this->requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 0 */
        self::assertEquals($expectedTotalCount, $response['total_count'] , $message);

        /* check list_items is null or empty */
        self::assertEmpty($response['items'] , $message);
    }

    /**
     * @param $response
     */
    public function expectedHasOneItemData($response, $expectedItemsData = null ,$gtCount = false){
        $expectedTotalCount = 1;
        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        unset($this->requestData['searchCriteria']['queryString']);
        AssertArrayContains::assert($this->requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount != 0 */
        if(!$gtCount) {
            self::assertEquals($expectedTotalCount, $response['total_count'], $message);
        }else{
            self::assertGreaterThanOrEqual($expectedTotalCount, $response['total_count'], $message);
        }
        /* check list_items is null or empty */
        self::assertNotEmpty($response['items'] , $message);

        /* check items count is 2 or not */
        self::assertCount($expectedTotalCount, $response['items'] , $message);

        if(!$expectedItemsData) {
            $expectedItemsData = [
                [
                    'sku' => Product::SKU_13,
                ],
            ];
        }

        /* check list_items is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }
}
