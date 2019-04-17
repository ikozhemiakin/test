<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SalesInformation
 * @package SoftLoft\Test\Model\ResourceModel
 */
class SalesInformation extends AbstractDb
{
    /**
     * SalesInformation constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * constructor
     */
    protected function _construct()
    {
        $this->_init('sales_order', 'entity_id');
    }
}