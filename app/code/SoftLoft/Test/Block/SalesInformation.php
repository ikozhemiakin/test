<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Block;

use \Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Phrase;
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
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $_orderCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * SalesInformation constructor.
     * @param Context $context
     * @param ProductInterface $entity
     * @param CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        /** @noinspection PhpDeprecationInspection */
        Context $context,
        ProductInterface $entity,
        CollectionFactory $orderCollectionFactory,
        ResourceConnection $resource,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_resource = $resource;
        $this->_data = $data;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_entity = $entity;
    }

    /**
     * Get qty of product in orders
     * @param $product
     * @return \Magento\Framework\DB\Select
     * @throws LocalizedException
     */
    public function getQty($product)
    {
        $productId = $product->getId();
        $extensionAttributes = $this->_entity->getExtensionAttributes();
        if (!$productId) {
            throw new LocalizedException(
                new Phrase('Product ID is not exist.')
            );
        }
        /** get current extension attributes from entity **/
        if (!isset($this->_data[0])) {
            $this->_data[0] = 'complete';
        } else {
            $ordersQty = $this->getLastOrdersQty($productId, $this->_data[0]);
            $extensionAttributes->setSalesInformation(['qty' => $this->getLastOrdersQty($ordersQty)]);
            $this->_entity->setExtensionAttributes($extensionAttributes);

            return $ordersQty;
        }
        $extensionAttributes->setSalesInformation(['qty' => $this->getLastOrdersQty($productId)]);
        $this->_entity->setExtensionAttributes($extensionAttributes);

        return $this->getLastOrdersQty($productId);
    }

    /**
     * Get last product orders qty
     *
     * @param $productId
     * @param null $status
     * @return \Magento\Framework\DB\Select
     */
    public function getLastOrdersQty($productId, $status = null)
    {
        $collection = $this->_orderCollectionFactory->create();
        $connection = $this->_resource->getConnection();
        $salesOrderItemTable = $connection->getTableName('sales_order_item');

        if ($status) {
            $collection->addFieldToFilter('status', ['eq' => $status]);
        }
        $collection->getSelect()
            ->join(
                $salesOrderItemTable,
                'main_table.entity_id = ' . $salesOrderItemTable . '.order_id'
            );

        $collection->getSelect()->where('product_id = ' . $productId)->group('main_table.entity_id');

        return $collection->getSize();
    }

    /**
     * Get last date of product order
     * @param $product
     * @return mixed
     * @throws LocalizedException
     */
    public function getLastOrderDate($product)
    {
        $productId = $product->getId();
        if (!$productId) {
            throw new LocalizedException(
                new Phrase('Product ID is not exist.')
            );
        }
        $connection = $this->_resource->getConnection();
        $extensionAttributes = $this->_entity->getExtensionAttributes();
        $salesOrderItemTable = $connection->getTableName('sales_order_item');
        $collection = $this->_orderCollectionFactory
            ->create()
            ->addAttributeToSelect('created_at')
            ->addFieldToFilter('status', ['eq' => 'complete'])
            ->setOrder(
                'created_at',
                'desc'
            );
        $collection->getSelect()
            ->join(
                $salesOrderItemTable,
                'main_table.entity_id = ' . $salesOrderItemTable . '.order_id',
                ['last_order_created_at' => 'created_at',]
            )
            ->where('product_id = ' . $productId)->limit(1);
        $collection->getFirstItem();
        if (!isset($collection->getData()[0]['created_at'])) {
            throw new LocalizedException(
                new Phrase('Complete order with this product is not exist.')
            );
        }
        $extensionAttributes->setSalesInformation(['last_order_date' => $collection->getData()[0]['created_at']]);
        $this->_entity->setExtensionAttributes($extensionAttributes);

        return $collection->getData()[0]['created_at'];
    }
}