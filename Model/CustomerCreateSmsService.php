<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Singsys\SmsGupshup\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
/**
* 
*/
class CustomerCreateSmsService implements \Singsys\SmsGupshup\Api\CustomerCreateSmsServiceInterface
{
    /** API Password */
    const SMS_GUPSHUP_PASSWD = 'sms_gupshup/configuration_option/password';

    /** API UserId */
    const SMS_GUPSHUP_USERID = 'sms_gupshup/configuration_option/userid';

    /** Message Type */
    const SMS_GUPSHUP_MSGTYPE = 'sms_gupshup/configuration_option/msg_type';

    /** Country Calling Code */
    const SMS_GUPSHUP_COUNTRY_CALLING_CODE = 'sms_gupshup/configuration_option/calling_code';

    /** SMS Method */
    const SMS_GUPSHUP_METHOD = 'sms_gupshup/configuration_option/method';

    /** Provider host url */
    const SMS_GUPSHUP_HOST = 'sms_gupshup/configuration_option/host';
    
    /** OTP Message */
    const SMS_GUPSHUP_OTP_MESSAGE = 'sms_gupshup/configuration_option/otp_message';
    
    /** Is enable module path */
    const SMS_GUPSHUP_IS_ENABLE = 'sms_gupshup/general/enabled';
    
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scopeConfig;	

    /** @var \Magento\Framework\Math\Random */
    protected $_random;

    /** @var \Magento\Framework\Encryption\EncryptorInterface */
    protected $_encryptor;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $_customerRepositoryInterface;

    /** @var \Magento\Customer\Model\CustomerFactory */
    protected $_customerFactory;

    /**
     * Initialize service
     *
     * @param 
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Math\Random $random,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_random = $random;
        $this->_encryptor = $encryptor;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerFactory = $customerFactory;

    }
    /**
     * Create OTP for customer verify mobile number.
     *
     * @param string $customerId
     * @param string $mobile_number
     * @param string $message
     * @return string Token created
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
	public function createCustomerSendSms($customerId,$mobile_number,$message)
	{
		$data['status'] = false;
		$data['message'] = '';
        
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $hostUrl = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_HOST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $api_password = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_PASSWD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $api_userid = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_USERID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $msg_type = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_MSGTYPE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $country_calling_code = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_COUNTRY_CALLING_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $method = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $otp_message = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_OTP_MESSAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $isEnabled = $this->_scopeConfig->getValue(self::SMS_GUPSHUP_IS_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
 
        if($isEnabled){
          
            $api_password = $this->_encryptor->decrypt($api_password);
            $code = $this->_random->getRandomNumber(1000,9999);

            $param['method']= $method;
            $param['send_to'] = $country_calling_code.$mobile_number;
            $param['msg'] = sprintf($otp_message,$code);
            $param['msg_type'] = $msg_type;
            $param['overide_dnd'] = "FALSE";
            $param['userid'] = $api_userid;
            $param['password'] = $api_password;
            $param['v'] = "1.1";
            $param['auth_scheme'] = "plain";
            $param['format'] = "text";
            $request = http_build_query($param);
            $ch = curl_init($hostUrl.'?'.$request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
            if (strpos($curl_scraped_page,'success') !== false) {
                $data['status'] = true;
                $data['message'] = (string)$code;
                $customer = $this->_customerFactory->create()->load($customerId)->getDataModel();
                $customer->setCustomAttribute('mobile_otp', $code);
                $customer->setCustomAttribute('otp_verified', '0');
                $this->_customerRepositoryInterface->save($customer);
            }
            else
            {
                $data['status'] = false;
            }
        }


        $data['token'] = '';
		//$data['params'] = $param;
		$data['message'] = $data['message'];
        return $data;
        // $jsonEncoder    = $objectManager->get('Singsys\Theme\Helper\Data');
        // $jsonEncoder->getResponseJson($data);
        // return ;
		
	}

    /**
     * Verify OTP for customer verify mobile number.
     *
     * @param string $customerId
     * @param string $otp
     * @return string OTP
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function createCustomerVerifyOtp($customerId,$otp)
    {
        $data['status'] = 'fail';
        $data['message'] = '';
        $data['mobile_otp'] = '';

        $customer = $this->getFilteredCustomerCollection($customerId,$otp);
        $data['mobile_otp'] = $customer->getMobileOtp();
        if($data['mobile_otp']==$otp){
            $customer = $this->_customerFactory->create()->load($customerId)->getDataModel();
            $customer->setCustomAttribute('otp_verified', '1');
            $this->_customerRepositoryInterface->save($customer);
            $data['status'] = 'success';
        }else{
            $data['mobile_otp'] = '';
            $data['message'] = __('Entered OTP is not valid. Please try again.');
        }
        return array($data);
    }

    public function getFilteredCustomerCollection($customerId,$otp) {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addFieldToFilter("entity_id", array("eq" => $customerId))
                ->addAttributeToFilter("mobile_otp", array("eq" => $otp))
                ->addAttributeToFilter("otp_verified", array("eq" => '0'))
                ->load()->getFirstItem();
    }

}