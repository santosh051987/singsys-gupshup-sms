<?php
/**
 * Singsys_SmsGupshup Magento Extension
 *
 * @category    Singsys
 * @package     Singsys_SmsGupshup
 * @author      Santosh Singh <santosh@singsys.com>
 * @website    http://www.singsys.com
 */

namespace Singsys\SmsGupshup\Observer;

use Magento\Framework\Event\ObserverInterface;

class SendOtpObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $mobile_number = $customer->getCustomAttribute('mobile_number');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $smsObject = $objectManager->create('Singsys\SmsGupshup\Model\CustomerCreateSmsService');
        $smsObject->createCustomerSendSms($customer->getId(),$mobile_number->getValue(),'');
        return $this;
    }
}