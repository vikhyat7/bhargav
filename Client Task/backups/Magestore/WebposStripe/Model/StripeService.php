<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Model;

/**
 * Webpos Stripe - Model - Stripe Service
 */
class StripeService implements \Magestore\WebposStripe\Api\StripeServiceInterface
{
    /**
     * @var \Magestore\WebposStripe\Api\StripeInterface
     */
    protected $stripe;

    /**
     * StripeService constructor.
     * @param \Magestore\WebposStripe\Api\StripeInterface $stripe
     */
    public function __construct(
        \Magestore\WebposStripe\Api\StripeInterface $stripe
    ) {
        $this->stripe = $stripe;
    }

    /**
     * @inheritDoc
     */
    public function isEnable()
    {
        $hasSDK = $this->stripe->validateRequiredSDK();
        $configs = $this->stripe->getConfig();
        return ($hasSDK
            && $configs['enable']
            && !empty($configs['publishable_key'])
            && !empty($configs['api_key'])
        ) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationError()
    {
        $message = '';
        $hasSDK = $this->stripe->validateRequiredSDK();
        $configs = $this->stripe->getConfig();
        if (!$hasSDK) {
            $message = __('Stripe SDK not found, '
                . 'please go to the configuration to get the instruction to install the SDK');
        } else {
            if ($configs['enable']) {
                if (empty($configs['publishable_key']) || empty($configs['api_key'])) {
                    $message = __('Stripe application client id and client secret are required');
                }
            } else {
                $message = __('Stripe integration is disabled');
            }
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function finishPayment($token, $amount)
    {
        return $this->stripe->completePayment($token, $amount);
    }

    /**
     * @inheritDoc
     */
    public function canConnectToApi()
    {
        return $this->stripe->canConnectToApi();
    }

    /**
     * @inheritDoc
     */
    public function refundPaymentCharge($request)
    {
        return $this->stripe->refundPaymentCharge($request);
    }
}
