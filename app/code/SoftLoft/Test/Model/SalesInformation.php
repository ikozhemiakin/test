<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Model;

use SoftLoft\Test\Api\Data\SalesInformationInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use SoftLoft\Test\Api\Data\SalesInformationExtensionInterface;

/**
 * Sales Information Model
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class SalesInformation extends AbstractExtensibleModel implements SalesInformationInterface
{
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
     * @param $qty
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
     * @param $lastOrderDate
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
