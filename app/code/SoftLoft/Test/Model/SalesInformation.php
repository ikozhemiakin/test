<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Model;

use SoftLoft\Test\Api\Data\SalesInformationInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use SoftLoft\Test\Api\Data\SalesInformationExtensionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Model\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

/**
 * Sales Information Model
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class SalesInformation extends AbstractExtensibleModel implements SalesInformationInterface, IdentityInterface
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
     * Constant
     */
    const CACHE_TAG = 'softloft_topic';

    /**
     * SalesInformation constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param ProductInterface $entity
     * @param CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        /** @noinspection PhpDeprecationInspection */
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        ProductInterface $entity,
        CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection);
        $this->_data = $data;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_entity = $entity;
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('SoftLoft\Test\Model\ResourceModel\SalesInformation');
    }

    /**
     * Get identities
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get qty of product in orders
     * @param object $product
     * @return \Magento\Framework\DB\Select
     * @throws LocalizedException
     */
    public function getProductQty($product)
    {
        $productId = $product->getId();
        /** get current extension attributes from entity **/
        $extensionAttributes = $this->_entity->getExtensionAttributes();
        if (!is_string($productId)) {
            throw new LocalizedException(
                new Phrase('Product ID is not exist.')
            );
        } elseif (isset($this->_data['status'])) {
            $ordersQty = $this->getLastOrdersQty($productId, $this->_data['status']);
        } else {
            $ordersQty = $this->getLastOrdersQty($productId);
        }
        $extensionAttributes->setSalesInformation(['qty' => $ordersQty]);
        $this->_entity->setExtensionAttributes($extensionAttributes);

        return $ordersQty;
    }

    /**
     * Get last product orders qty
     *
     * @param string $productId
     * @param null $status
     * @return \Magento\Framework\DB\Select
     */
    public function getLastOrdersQty($productId, $status = null)
    {
        $collection = $this->_orderCollectionFactory->create();
        $salesOrderItemTable = $this->getResource()->getTable('sales_order_item');
        if (is_string($status)) {
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
     * @param object $product
     * @return mixed
     * @throws LocalizedException
     */
    public function getLastProductOrderDate($product)
    {
        $productId = $product->getId();
        if (!$productId) {
            throw new LocalizedException(
                new Phrase('Product ID is not exist.')
            );
        }
        $salesOrderItemTable = $this->getResource()->getTable('sales_order_item');
        $extensionAttributes = $this->_entity->getExtensionAttributes();
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
            return '';
        }
        $extensionAttributes->setSalesInformation(['last_order_date' => $collection->getData()[0]['created_at']]);
        $this->_entity->setExtensionAttributes($extensionAttributes);

        return $collection->getData()[0]['created_at'];
    }

    /**
     * Get qty of product in orders
     *
     * @return integer|null
     */
    public function getQty()
    {
        return $this->getData(self::KEY_QTY);
    }

    /**
     * Set qty of product in orders
     *
     * @param integer $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->setData(self::KEY_QTY, $qty);
    }

    /**
     * Get last date of product order
     *
     * @return string|null
     */
    public function getLastOrderDate()
    {
        return $this->getData(self::KEY_LAST_ORDER);
    }

    /**
     * Set last date of product order
     *
     * @param string $lastOrderDate
     * @return $this
     */
    public function setLastOrderDate($lastOrderDate)
    {
        return $this->setData(self::KEY_LAST_ORDER, $lastOrderDate);
    }

    /**
     * {@inheritdoc}
     *
     * @return \SoftLoft\Test\Api\Data\SalesInformationInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \SoftLoft\Test\Api\Data\SalesInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        /** @noinspection PhpUndefinedClassInspection */
        SalesInformationExtensionInterface $extensionAttributes)
    {
        /** @noinspection PhpParamsInspection */
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
