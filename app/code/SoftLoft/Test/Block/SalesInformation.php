<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SoftLoft\Test\Api\Data\SalesInformationInterface;

/**
 * SalesInformation block
 * Class SalesInformation
 * @package SoftLoft\Test\Block
 */
class SalesInformation extends Template
{
    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    private $_entity;

    /**
     * SalesInformation constructor.
     * @param Context $context
     * @param SalesInformationInterface $entity
     * @param array $data
     */
    public function __construct(
        /** @noinspection PhpDeprecationInspection */
        Context $context,
        SalesInformationInterface $entity,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_entity = $entity;
    }

    /**
     * Get last product orders qty
     * @param $product
     * @return mixed
     */
    public function getQty($product)
    {
        return $this->_entity->getProductQty($product);
    }

    /**
     * Get last product orders qty
     * @param $product
     * @return mixed
     */
    public function getLastOrderDate($product)
    {
        return $this->_entity->getLastProductOrderDate($product);
    }
}