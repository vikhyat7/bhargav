<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

use Elasticsearch\Common\Exceptions\MaxRetriesException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magestore\Giftvoucher\Model\Status;

/**
 * Giftvoucher Model
 *
 * @author      Magestore Developer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftvoucher extends \Magento\Rule\Model\AbstractModel implements
    \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface
{

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_conditionsInstance;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_actionsInstance;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Email\Model\Template
     */
    protected $_emailTemplate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var array
     */
    protected $_calculators = [];

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory
     */
    protected $_historyRepositoryFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Construct Giftvoucher
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Email\Model\Template $emailTemplate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory
     * @param \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory $historyRepositoryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $_currencyFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @internal param \Magestore\Giftvoucher\Api\HistoryRepositoryInterface $historyRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Email\Model\Template $emailTemplate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory,
        \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory $historyRepositoryFactory,
        \Magento\Directory\Model\CurrencyFactory $_currencyFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->_conditionsInstance = $conditionsInstance;
        $this->_actionsInstance = $actionsInstance;
        $this->_helperData = $helperData;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_urlBuilder = $urlBuilder;
        $this->_emailTemplate = $emailTemplate;
        $this->_dateTime = $dateTime;
        $this->_calculatorFactory = $calculatorFactory;
        $this->_historyFactory = $historyFactory;
        $this->_historyRepositoryFactory = $historyRepositoryFactory;
        $this->_currencyFactory = $_currencyFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher::class);
    }

    /**
     * Load Gift Card by gift code
     *
     * @param string $code
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'gift_code');
    }

    /**
     * Load
     *
     * @param int $id
     * @param null|string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        parent::load($id, $field);

        $timeSite = date(
            "Y-m-d H:i:s",
            $this->_dateTime->timestamp(time())
        );
        if ($this->getIsDeleted()) {
            return $this;
        }

        if ($this->getStatus() == Status::STATUS_ACTIVE
            && $this->getExpiredAt() && $this->getExpiredAt() < $timeSite
        ) {
            $this->setStatus(Status::STATUS_EXPIRED);
        }
        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    public function getConditionsInstance()
    {
        return $this->_conditionsInstance->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    public function getActionsInstance()
    {
        return $this->_actionsInstance->create();
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $rule
     * @return $this
     * @internal param array $data
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions([])->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions([])->loadArray($arr['actions'][1], 'actions');
        }

        return $this;
    }

    /**
     * Round Price
     *
     * @param float $price
     * @param string $type
     * @param bool $negative
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_calculators[$type])) {
                $this->_calculators[$type] = $this->_calculatorFactory->create(
                    ['scope' => $this->_storeManager->getStore()]
                );
            }
            $price = $this->_calculators[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * Get the base balance of gift code
     *
     * @param string $storeId
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseBalance($storeId = null)
    {
        if (!$this->hasData('base_balance')) {
            $baseBalance = 0;
            if ($rate = $this->_storeManager->getStore($storeId)
                ->getBaseCurrency()->getRate($this->getData('currency'))
            ) {
                $baseBalance = $this->getBalance() / $rate;
            }
            $this->setData('base_balance', $baseBalance);
        }
        return $this->getData('base_balance');
    }

    /**
     * Before Save
     *
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $timeSite = date(
            "Y-m-d H:i:s",
            $this->_dateTime->timestamp(time())
        );
        if (!$this->getId()) {
            $this->setAction(\Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE);
        }

        if (!$this->getGiftcardTemplateId()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $defaultTemplate = $objectManager
                ->get(\Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection::class)
                ->getFirstItem();
            if ($defaultTemplate->getId()) {
                $this->setGiftcardTemplateId($defaultTemplate->getId());
                $images = $defaultTemplate->getImages();
                $imagesArray = explode(',', $images);
                if (isset($imagesArray[0])) {
                    $this->setGiftcardTemplateImage($imagesArray[0]);
                }
            }
        }

        if ($this->getStoreId() == null) {
            $this->setStoreId(0);
        }

        if (!$this->getStatus()) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_PENDING);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED
            && $this->roundPrice($this->getBalance()) > 0
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
            && $this->roundPrice($this->getBalance()) == 0
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_USED);
        }

        if (($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
                || $this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED
                || $this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_PENDING)
            && $this->getExpiredAt() && $this->getExpiredAt() < $timeSite
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED
            && $this->getExpiredAt() && $this->getExpiredAt() > date('Y-m-d')
        ) {
            $this->setExpiredAt(date('Y-m-d'));
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED
            && !$this->getExpiredAt()
        ) {
            $this->setExpiredAt(date('Y-m-d'));
        }

        if ($this->getExpiredAt() && $this->getExpiredAt() < date('Y-m-d')) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED);
        }

        if (!$this->getGiftCode()) {
            $this->setGiftCode(
                $this->_scopeConfig->getValue('giftvoucher/general/pattern')
            );
        }
        if ($this->_codeIsExpression()) {
            $this->setGiftCode($this->_getGiftCode());
        } else {
            if ($this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE) {
                if ($this->getResource()->giftcodeExist($this->getGiftCode())) {
                    throw new AlreadyExistsException(__('Gift code is existed!'));
                }
            }
        }

        if (!$this->_registry->registry('giftvoucher_conditions')) {
            $this->_registry->register('giftvoucher_conditions', true);
        } else {
            if (!$this->getGenerateGiftcode()) {
                $data = $this->getData();
                if (isset($data['conditions_serialized'])) {
                    unset($data['conditions_serialized']);
                }
                if (isset($data['actions_serialized'])) {
                    unset($data['actions_serialized']);
                }
                $this->setData($data);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->getIncludeHistory() && $this->getAction()) {
            $history = $this->_historyFactory->create()
                ->setData($this->getData())
                ->setData(
                    'created_at',
                    (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
                );
            if ($this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_UPDATE
                || $this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_MASS_UPDATE
            ) {
                $history->setData('customer_id', null)
                    ->setData('customer_email', null)
                    ->setData('amount', $this->getBalance());
            }

            try {
                $this->_historyRepositoryFactory->create()->save($history);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * Code Is Expression
     *
     * @return bool|int
     */
    public function _codeIsExpression()
    {
        return $this->_helperData->isExpression($this->getGiftCode());
    }

    /**
     * Get Gift Code
     *
     * @return string
     * @throws \Exception
     */
    public function _getGiftCode()
    {
        $helper = $this->_helperData;
        $code = $helper->calcCode($this->getGiftCode());
        $times = 10;
        while ($this->getResource()->giftcodeExist($code) && $times) {
            $code = $helper->calcCode($this->getGiftCode());
            $times--;
            if ($times == 0) {
                throw new MaxRetriesException(__('Exceeded maximum retries to find available random gift card code!'));
            }
        }
        return $code;
    }

    /**
     * Add To Session
     *
     * @param mixed $session
     * @return $this
     */
    public function addToSession($session = null)
    {
        if ($session === null) {
            $session = $this->_helperData->getCheckoutSession();
        }
        if ($codes = $session->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesArray[] = $this->getGiftCode();
            $codes = implode(',', array_unique($codesArray));
        } else {
            $codes = $this->getGiftCode();
        }
        $session->setGiftCodes($codes);
        return $this;
    }

    /**
     * Send Email
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function sendEmail()
    {
        $store = $this->_storeManager->getStore($this->getStoreId());
        $storeId = $store->getStoreId();
        $mailSent = 0;
        if ($this->getCustomerEmail()) {
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('self', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'giftvoucher' => $this,
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'noactive' => ($this->getStatus() == Status::STATUS_ACTIVE)
                            ? 0 : 1,
                        'expiredat' => $this->getExpiredAt() ?
                            $this->_dateTime->date('M d, Y', $this->getExpiredAt()) : '',
                        'message' => $this->getFormatedMessage(),
                        'note' => $this->getEmailNotes(),
                        'description' => $this->getDescription(),
                        'logo' => $this->getPrintLogo(),
                        'url' => $this->getPrintTemplate(),
                        'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId()),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getCustomerEmail(),
                    $this->getCustomerName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
            $mailSent++;
        }
        if ($this->getRecipientEmail()) {
            $mailSent += $this->sendEmailToRecipient();
        }
        if ($this->getRecipientEmail() || $this->getCustomerEmail()) {
            try {
                if ($this->getData('recipient_address')) {
                    $this->setIsSent(2);
                } else {
                    $this->setIsSent(true);
                }
                if (!$this->getNotResave()) {
                    $this->save();
                }
            } catch (\Exception $ex) {
                $this->_logger->critical($ex);
            }
        }

        $this->setEmailSent($mailSent);
        return $this;
    }

    /**
     * Send email to Gift Voucher Receipient
     *
     * @return int The number of email sent
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function sendEmailToRecipient()
    {
        $allowStatus = explode(',', $this->_helperData->getEmailConfig('only_complete', $this->getStoreId()));
        if (!is_array($allowStatus)) {
            $allowStatus = [];
        }
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient')
            && in_array($this->getStatus(), $allowStatus)
        ) {
            try {
                $store = $this->_storeManager->getStore($this->getStoreId());
                $storeId = $store->getStoreId();

                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'giftvoucher' => $this,
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'noactive' => ($this->getStatus() == Status::STATUS_ACTIVE)
                            ? 0 : 1,
                        'expiredat' => $this->getExpiredAt() ?
                            $this->_dateTime->date('M d, Y', $this->getExpiredAt()) : '',
                        'message' => $this->getFormatedMessage(),
                        'note' => $this->getEmailNotes(),
                        'logo' => $this->getPrintLogo(),
                        'url' => $this->getPrintTemplate(),
                        'addurl' => $store->getBaseUrl()
                            . '/giftvoucher/index/addlist/giftvouchercode/'
                            . $this->getGiftCode(),
                        'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId())
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getRecipientEmail(),
                    $this->getRecipientName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }

            try {
                if (!$this->getData('recipient_address')) {
                    $this->setIsSent(true);
                } else {
                    $this->setIsSent(2);
                }
                if (!$this->getNotResave()) {
                    $this->save();
                }
            } catch (\Exception $ex) {
                $this->_logger->critical($ex);
            }
            return 1;
        }
        return 0;
    }

    /**
     * Send the success notification email
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendEmailSuccess()
    {
        if ($this->getCustomerEmail()) {
            $store = $this->_storeManager->getStore($this->getStoreId());
            $storeId = $store->getStoreId();

            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template_success', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'name' => $this->getCustomerName(),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getCustomerEmail(),
                    $this->getCustomerName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
        }
        return $this;
    }

    /**
     * Send the refund notification email
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendEmailRefundToRecipient()
    {
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient')) {
            $store = $this->_storeManager->getStore($this->getStoreId());
            $storeId = $store->getStoreId();
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template_refund', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'sendername' => $this->getCustomerName(),
                        'receivename' => $this->getRecipientName(),
                        'code' => $this->getGiftCode(),
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'message' => $this->getFormatedMessage(),
                        'description' => $this->getDescription(),
                        'addurl' => $this->_urlBuilder->getUrl('giftvoucher/index/addlist', [
                            'giftvouchercode' => $this->getGiftCode()
                        ]),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getRecipientEmail(),
                    $this->getRecipientName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
        }
        return $this;
    }

    /**
     * Get Status Label
     *
     * @return mixed
     */
    public function getStatusLabel()
    {
        $statusArray = $this->_objectManager->get(\Magestore\Giftvoucher\Model\Status::class)->getOptionArray();
        return $statusArray[$this->getStatus()];
    }

    /**
     * Get Formated Message
     *
     * @return mixed
     */
    public function getFormatedMessage()
    {
        return str_replace("\n", "<br/>", $this->getMessage());
    }

    /**
     * Get the email notes
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEmailNotes()
    {
        if (!$this->hasData('email_notes')) {
            $notes = $this->_scopeConfig->getValue(
                'giftvoucher/email/note',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
            $notes = str_replace(
                [
                    '{store_url}',
                    '{store_name}',
                    '{store_address}'

                ],
                [
                    $this->_storeManager->getStore($this->getStoreId())->getBaseUrl(),
                    $this->_storeManager->getStore($this->getStoreId())->getFrontendName(),
                    $this->_scopeConfig->getValue(
                        'general/store_information/address',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $this->getStoreId()
                    )

                ],
                $notes
            );
            $this->setData('email_notes', $notes);
        }
        return $this->getData('email_notes');
    }

    /**
     * Get the print logo
     *
     * @return string|boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPrintLogo()
    {
        $image = $this->_scopeConfig->getValue('giftvoucher/print_voucher/logo', 'store', $this->getStoreId());
        if ($image) {
            $image = $this->_storeManager->getStore($this->getStoreId())->getBaseUrl('media')
                . 'giftvoucher/pdf/logo/' . $image;
            return $image;
        }
        return false;
    }

    /**
     * Returns the formatted balance
     *
     * @return string
     */
    public function getBalanceFormated()
    {
        $currency = $this->_currencyFactory->create()->load($this->getCurrency());
        return $currency->format($this->getBalance());
    }

    /**
     * Gift code balance with currency format
     *
     * @return string
     */
    public function getGiftcodeBalance()
    {
        $currency = $this->_currencyFactory->create()->load($this->getCurrency());
        return $currency->format($this->getBalance(), [], false);
    }

    /**
     * Get the print notes
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPrintNotes()
    {
        if (!$this->hasData('print_notes')) {
            $notes = $this->_scopeConfig->getValue('giftvoucher/print_voucher/note', 'store', $this->getStoreId());
            $notes = str_replace(
                [
                    '{store_url}',
                    '{store_name}',
                    '{store_address}'
                ],
                [
                    '<span class="print-notes">' . $this->_storeManager->getStore($this->getStoreId())->getBaseUrl()
                        . '</span>',
                    '<span class="print-notes">'
                        . $this->_storeManager->getStore($this->getStoreId())->getFrontendName()
                        . '</span>',
                    '<span class="print-notes">' . $this->_scopeConfig->getValue(
                        'general/store_information/address',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $this->getStoreId()
                    ) . '</span>'
                ],
                $notes
            );
            $this->setData('print_notes', $notes);
        }
        return $this->getData('print_notes');
    }

    /**
     * Get the list customer that used this code
     *
     * @return array
     */
    public function getCustomerIdsUsed()
    {
        $collection = $this->_objectManager
            ->create(\Magestore\Giftvoucher\Model\ResourceModel\History\Collection::class)
            ->addFieldToFilter('main_table.giftvoucher_id', $this->getId())
            ->addFieldToFilter('main_table.action', \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER);

        $collection->joinSalesOrder();
        $customerIds = [];
        foreach ($collection as $item) {
            $customerIds[] = $item->getData('order_customer_id');
        }
        return $customerIds;
    }

    /**
     * Check gift code is valid in current website
     *
     * @param null|int $storeId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isValidWebsite($storeId = null)
    {
        if ($this->getStoreId()) {
            $currentWebsite = $this->_storeManager->getStore($storeId)->getWebsiteId();
            $giftWebsite = $this->_storeManager->getStore($this->getStoreId())->getWebsiteId();
            return $currentWebsite == $giftWebsite;
        }
        return true;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getGiftvoucherId()
    {
        return $this->getData(self::GIFTVOUCHER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGiftvoucherId($giftvoucherId)
    {
        return $this->setData(self::GIFTVOUCHER_ID, $giftvoucherId);
    }

    /**
     * Get Gift code
     *
     * @return string|null
     */
    public function getGiftCode()
    {
        return $this->getData(self::GIFT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setGiftCode($giftCode)
    {
        return $this->setData(self::GIFT_CODE, $giftCode);
    }

    /**
     * Get Gift code balance
     *
     * @return string|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * @inheritDoc
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Get Gift code currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setExpiredAt($expiredAt)
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setRecipientName($recipientName)
    {
        return $this->setData(self::RECIPIENT_NAME, $recipientName);
    }

    /**
     * @inheritDoc
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * @inheritDoc
     */
    public function getRecipientAddress()
    {
        return $this->getData(self::RECIPIENT_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setRecipientAddress($recipientAddress)
    {
        return $this->setData(self::RECIPIENT_ADDRESS, $recipientAddress);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @inheritDoc
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @inheritDoc
     */
    public function getDayToSend()
    {
        return $this->getData(self::DAY_TO_SEND);
    }

    /**
     * @inheritDoc
     */
    public function setDayToSend($dayToSend)
    {
        return $this->setData(self::DAY_TO_SEND, $dayToSend);
    }

    /**
     * @inheritDoc
     */
    public function getIsSent()
    {
        return $this->getData(self::IS_SENT);
    }

    /**
     * @inheritDoc
     */
    public function setIsSent($isSent)
    {
        return $this->setData(self::IS_SENT, $isSent);
    }

    /**
     * @inheritDoc
     */
    public function getShippedToCustomer()
    {
        return $this->getData(self::SHIPPED_TO_CUSTOMER);
    }

    /**
     * @inheritDoc
     */
    public function setShippedToCustomer($shippedToCustomer)
    {
        return $this->setData(self::SHIPPED_TO_CUSTOMER, $shippedToCustomer);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedForm()
    {
        return $this->getData(self::CREATED_FORM);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedForm($createdForm)
    {
        return $this->setData(self::CREATED_FORM, $createdForm);
    }

    /**
     * @inheritDoc
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getGiftvoucherComments()
    {
        return $this->getData(self::GIFTVOUCHER_CONMENTS);
    }

    /**
     * @inheritDoc
     */
    public function setGiftvoucherComments($giftvoucherComments)
    {
        return $this->setData(self::GIFTVOUCHER_CONMENTS, $giftvoucherComments);
    }

    /**
     * @inheritDoc
     */
    public function getEmailSender()
    {
        return $this->getData(self::EMAIL_SENDER);
    }

    /**
     * @inheritDoc
     */
    public function setEmailSender($emailSender)
    {
        return $this->setData(self::EMAIL_SENDER, $emailSender);
    }

    /**
     * @inheritDoc
     */
    public function getNotifySuccess()
    {
        return $this->getData(self::NOTIFY_SUCCESS);
    }

    /**
     * @inheritDoc
     */
    public function setNotifySuccess($notifySuccess)
    {
        return $this->setData(self::NOTIFY_SUCCESS, $notifySuccess);
    }

    /**
     * @inheritDoc
     */
    public function getGiftcardCustomImage()
    {
        return $this->getData(self::GIFTCARD_CUSTOM_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setGiftcardCustomImage($giftcardCustomImage)
    {
        return $this->setData(self::GIFTCARD_CUSTOM_IMAGE, $giftcardCustomImage);
    }

    /**
     * @inheritDoc
     */
    public function getGiftcardTemplateId()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGiftcardTemplateId($giftcardTemplateId)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $giftcardTemplateId);
    }

    /**
     * @inheritDoc
     */
    public function getGiftcardTemplateImage()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_IMAGE, $giftcardTemplateImage);
    }

    /**
     * @inheritDoc
     */
    public function getActionsSerialized()
    {
        return $this->getData(self::ACTIONS_SERIALIZED);
    }

    /**
     * @inheritDoc
     */
    public function setActionsSerialized($actionsSerialized)
    {
        return $this->setData(self::ACTIONS_SERIALIZED, $actionsSerialized);
    }

    /**
     * @inheritDoc
     */
    public function getTimezoneToSend()
    {
        return $this->getData(self::TIMEZONE_TO_SEND);
    }

    /**
     * @inheritDoc
     */
    public function setTimezoneToSend($timezoneToSend)
    {
        return $this->setData(self::TIMEZONE_TO_SEND, $timezoneToSend);
    }

    /**
     * @inheritDoc
     */
    public function getDayStore()
    {
        return $this->getData(self::DAY_STORE);
    }

    /**
     * @inheritDoc
     */
    public function setDayStore($dayStore)
    {
        return $this->setData(self::DAY_STORE, $dayStore);
    }

    /**
     * @inheritDoc
     */
    public function getUsed()
    {
        return $this->getData(self::USED);
    }

    /**
     * @inheritDoc
     */
    public function setUsed($used)
    {
        return $this->setData(self::USED, $used);
    }

    /**
     * @inheritDoc
     */
    public function getSetId()
    {
        return $this->getData(self::SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSetId($setId)
    {
        return $this->setData(self::SET_ID, $setId);
    }

    /**
     * Get conditions field set id.
     *
     * @param string $formName
     * @return string
     * @since 100.1.0
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Get actions field set id.
     *
     * @param string $formName
     * @return string
     * @since 100.1.0
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }
}
