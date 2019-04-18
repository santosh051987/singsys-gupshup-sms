<?php
/**
 * Singsys Pte Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Singsys.com license that is
 * available through the world-wide-web at this URL:
 * https://www.singsys.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Singsys
 * @package     Singsys_SmsGupshup
 * @copyright   Copyright (c) 2009 Singsys (http://www.singsys.com/)
 * @license     https://www.singsys.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Singsys\SmsGupshup\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_storeManager;
	protected $_objectManager;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		ObjectManagerInterface $objectManager
	)
	{
		$this->_storeManager   = $storeManager;
		$this->_objectManager = $objectManager;

		parent::__construct($context, $objectManager, $storeManager);
	}
}