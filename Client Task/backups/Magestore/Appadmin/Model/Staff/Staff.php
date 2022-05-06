<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff;

use Magestore\Appadmin\Api\Data\Staff\StaffInterface;

/**
 * Model Staff
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Staff extends \Magento\Framework\Model\AbstractModel implements \Magestore\Appadmin\Api\Data\Staff\StaffInterface
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $staffCollectionFactory;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff
     */
    protected $staffResource;
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;
    /**
     * @var \Magestore\Appadmin\Api\Staff\RuleRepositoryInterface
     */
    protected $ruleRepository;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location
     */
    protected $locationResource;

    protected $currentData = [];

    /**
     * Staff constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff $resource
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection $resourceCollection
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Appadmin\Api\Staff\RuleRepositoryInterface $ruleRepository
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location $locationResource
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff $resource,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection $resourceCollection,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Appadmin\Api\Staff\RuleRepositoryInterface $ruleRepository,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Webpos\Model\ResourceModel\Location\Location $locationResource,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->encryptor = $encryptor;
        $this->staffCollectionFactory = $staffCollectionFactory;
        $this->staffResource = $resource;
        $this->dispatchService = $dispatchService;
        $this->ruleRepository = $ruleRepository;
        $this->staffRepository = $staffRepository;
        $this->locationResource = $locationResource;
    }

    /**
     * Get Encoded Password
     *
     * @param string $password
     * @return string
     */
    public function getEncodedPassword($password)
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * Before Save
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $data = [];
        if ($this->getNewPassword()) {
            $data['password'] = $this->getEncodedPassword($this->getNewPassword());

            // dispatch event force sign out when change password
            if (!$this->isObjectNew()) {
                $this->dispatchService->dispatchEventForceSignOut($this->getStaffId());
            }
        } elseif ($this->getPassword() && !$this->getId() && !$this->getNotEncode()) {
            $data['password'] = $this->getEncodedPassword($this->getPassword());
        }
        $this->addData($data);

        // set data object before save
        if (!$this->isObjectNew()) {
            $currentObject = $this->staffRepository->getById($this->getStaffId());
            $this->currentData = $currentObject->getData();
        }

        return parent::beforeSave();
    }

    /**
     * After Save
     *
     * @return Staff
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        if (!$this->isObjectNew()) {
            // dispatch event force sign out when not have permission
            // or disable staff
            if ((!$this->ruleRepository->isAllowPermission('Magestore_Webpos::manage_pos', $this->getStaffId())
                || $this->getStatus() == \Magestore\Appadmin\Model\Source\Adminhtml\Status::STATUS_DISABLED)) {
                $this->dispatchService->dispatchEventForceSignOut($this->getStaffId());
            }

            // dispatch event force change pos when remove some location assigned
            if ($this->hasDataChangeForField('location_ids')) {
                // get list location id which has been deleted
                $oldLocationIds = explode(',', $this->currentData['location_ids']);
                $newLocationIds = explode(',', $this->getData('location_ids'));
                $locationDeleted = [];
                foreach ($oldLocationIds as $locationId) {
                    if (!in_array($locationId, $newLocationIds)) {
                        $locationDeleted[] = $locationId;
                    }
                }

                $this->forceChangePosForStaff($locationDeleted);
            }
        }
        return parent::afterSave();
    }

    /**
     * Force Change Pos For Staff
     *
     * @param array $locationDeleted
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceChangePosForStaff($locationDeleted)
    {
        foreach ($locationDeleted as $locationId) {
            /** @var \Magestore\Webpos\Model\ResourceModel\Location\Location $resourceLocation */
            $resourceLocation = $this->locationResource;
            $select = $resourceLocation->getConnection()->select();
            $select->from(['e' => $resourceLocation->getMainTable()]);
            $select->join(
                ['pos' => $resourceLocation->getTable('webpos_pos')],
                'pos.location_id = e.' . $resourceLocation->getIdFieldName(),
                []
            );
            $select->where('staff_id = ' . $this->getStaffId());
            $select->where('pos.location_id = ' . $locationId);
            $select->columns('pos.pos_id');

            $data = $resourceLocation->getConnection()->fetchAll($select);
            foreach ($data as $datum) {
                // dispatch event force change pos
                $this->dispatchService->dispatchEventForceChangePos($this->getStaffId(), $datum['pos_id']);
            }
        }
    }

    /**
     * Before Delete
     *
     * @return Staff
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $this->dispatchService->dispatchEventForceSignOut($this->getStaffId());
        return parent::beforeDelete();
    }

    /**
     * Check if data has change for field
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasDataChangeForField($fieldName)
    {
        return !($this->currentData[$fieldName] == $this->getData($fieldName));
    }

    /**
     * LoadByUsername
     *
     * @param string $username
     * @return $this
     */
    public function loadByUsername($username)
    {
        $staffCollection = $this->staffCollectionFactory->create()->addFieldToFilter(self::USER_NAME, $username)
            ->addFieldToFilter(self::STATUS, self::STATUS_ENABLED);
        if ($id = $staffCollection->getFirstItem()->getId()) {
            $this->staffResource->load($this, $id);
        }
        return $this;
    }

    /**
     * Authenticate
     *
     * @param string $username
     * @param string $password
     * @return $this|bool
     * @throws \Exception
     */
    public function authenticate($username, $password)
    {
        $this->loadByUsername($username);
        if (!$this->validatePassword($password)) {
            return false;
        }
        return $this;
    }

    /**
     * Validate
     *
     * @return array|bool
     */
    public function validate()
    {
        $errors = [];
        if ($this->hasNewPassword()) {
            if (strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
                $errors[] = __('Password must be at least of %1 characters.', self::MIN_PASSWORD_LENGTH);
            }

            if (!preg_match('/[a-z]/iu', $this->getNewPassword())
                || !preg_match('/[0-9]/u', $this->getNewPassword())) {
                $errors[] = __('Password must include both numeric and alphabetic characters.');
            }

            if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
                $errors[] = __('Password confirmation must be same as password.');
            }
        }
        if ($this->userExists()) {
            $errors[] = __('A user with the same user name already exists.');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Validate Password
     *
     * @param string $password
     * @return bool
     * @throws \Exception
     */
    public function validatePassword($password)
    {
        $hash = $this->getPassword();
        if (!$hash) {
            return false;
        }
        return $this->encryptor->validateHash($password, $hash);
    }

    /**
     * Get staff id
     *
     * @return string|null
     * @api
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set staff id
     *
     * @param string $staffId
     * @return $this
     * @api
     */
    public function setStaffId($staffId)
    {
        $this->setData(self::STAFF_ID, $staffId);
        return $this;
    }

    /**
     * Get user name
     *
     * @return string|null
     * @api
     */
    public function getUsername()
    {
        return $this->getData(self::USER_NAME);
    }

    /**
     * Set user name
     *
     * @param string $username
     * @return $this
     * @api
     */
    public function setUsername($username)
    {
        $this->setData(self::USER_NAME, $username);
        return $this;
    }

    /**
     * Get password params
     *
     * @return string|null
     * @api
     */
    public function getPassword()
    {
        return $this->getData(self::PASSWORD);
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     * @api
     */
    public function setPassword($password)
    {
        $this->setData(self::PASSWORD, $password);
        return $this;
    }

    /**
     * Get display name
     *
     * @return string|null
     * @api
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set display name
     *
     * @param string $name
     * @return $this
     * @api
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     * @api
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set display name
     *
     * @param string $email
     * @return $this
     * @api
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
        return $this;
    }

    /**
     * Get customer group
     *
     * @return string|null
     * @api
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * Set customer group
     *
     * @param string $customerGroups
     * @return $this
     * @api
     */
    public function setCustomerGroups($customerGroups)
    {
        $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
        return $this;
    }

    /**
     * Get location ids
     *
     * @return string|null
     * @api
     */
    public function getLocationIds()
    {
        return $this->getData(self::LOCATION_IDS);
    }

    /**
     * Set location ids
     *
     * @param string $locationIds
     * @return $this
     * @api
     */
    public function setLocationIds($locationIds)
    {
        $this->setData(self::LOCATION_IDS, $locationIds);
        return $this;
    }

    /**
     * Get role id
     *
     * @return string|null
     * @api
     */
    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Set role id
     *
     * @param string $roleId
     * @return $this
     * @api
     */
    public function setRoleId($roleId)
    {
        $this->setData(self::ROLE_ID, $roleId);
        return $this;
    }

    /**
     * Get status
     *
     * @return string|null
     * @api
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     * @api
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * Get pos id
     *
     * @return string|null
     * @api
     */
    public function getPosIds()
    {
        return $this->getData(self::POS_IDS);
    }

    /**
     * Set pos id
     *
     * @param string $posIds
     * @return $this
     * @api
     */
    public function setPosIds($posIds)
    {
        $this->setData(self::POS_IDS, $posIds);
        return $this;
    }

    /**
     * User Exists
     *
     * @return bool
     */
    public function userExists()
    {
        $username = $this->getUsername();
        $check = $this->staffCollectionFactory->create()->addFieldToFilter(self::USER_NAME, $username);
        if ($check->getFirstItem()->getId() && $this->getId() != $check->getFirstItem()->getId()) {
            return true;
        }
        return false;
    }
}
