<?php

namespace Singsys\SmsGupshup\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;

class CustomerSetup extends EavSetup {

	protected $eavConfig;

	public function __construct(
		ModuleDataSetupInterface $setup,
		Context $context,
		CacheInterface $cache,
		CollectionFactory $attrGroupCollectionFactory,
		Config $eavConfig
		) {
		$this->eavConfig = $eavConfig;
		parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
	} 

	public function installAttributes($customerSetup) {
		$this->installCustomerAttributes($customerSetup);
		$this->installCustomerAddressAttributes($customerSetup);
	} 

	public function installCustomerAttributes($customerSetup) {
		$customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY,
			'mobile_otp',
			[
				'label' => 'OTP',
				'system' => 0,
				'position' => 100,
	            'sort_order' =>100,
	            'visible' =>  false,
				'note' => '',
	            'type' => 'varchar',
	            'input' => 'text',
			]
			);

		$customerSetup->getEavConfig()->getAttribute('customer', 'mobile_otp')
		->setData('is_user_defined',1)
		->setData('is_required',1)
		->setData('default_value','')
		->setData('used_in_forms', 
			['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout']
		)
		->save();

		$customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY,
			'otp_verified',
			[
				'label' => 'OTP Verified',
				'system' => 0,
				'position' => 101,
	            'sort_order' =>101,
	            'visible' =>  false,
				'note' => '',
	            'type' => 'varchar',
	            'input' => 'text',
			]
			);

		$customerSetup->getEavConfig()->getAttribute('customer', 'otp_verified')
		->setData('is_user_defined',1)
		->setData('is_required',0)
		->setData('default_value','')
		->setData('used_in_forms', 
			['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout']
		)
		->save();				
	} 

	public function installCustomerAddressAttributes($customerSetup) {
			
	} 

	public function getEavConfig() {
		return $this->eavConfig;
	} 
} 