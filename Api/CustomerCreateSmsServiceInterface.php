<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Singsys\SmsGupshup\Api;

/**
 * Interface providing sms to created customer
 *
 * @api
 * @since 100.0.2
 */
interface CustomerCreateSmsServiceInterface
{
    /**
     * Create OTP for customer verify mobile number.
     *
     * @param string $customerId
     * @param string $mobile_number
     * @param string $message
     * @return string Token created
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function createCustomerSendSms($customerId,$mobile_number,$message);

    /**
     * Verify OTP for customer verify mobile number.
     *
     * @param string $customerId
     * @param string $otp
     * @return string OTP
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function createCustomerVerifyOtp($customerId,$otp);
}
