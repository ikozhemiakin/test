<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SalesInformationInterface
 * @api
 */
interface SalesInformationInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_QTY = 'qty';

    /**
     * Last order constant
     */
    const KEY_LAST_ORDER = 'last_order_date';

    /**
     * Get qty of product in orders
     *
     * @return integer|null
     */
    public function getQty();

    /**
     * Set qty using order status
     *
     * @param $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get last date of product order
     *
     * @return string|null
     */
    public function getLastOrderDate();

    /**
     * Set last date of product order
     *
     * @param $lastOrderDate
     * @return $this
     */
    public function setLastOrderDate($lastOrderDate);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \SoftLoft\Test\Api\Data\SalesInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     * @param \SoftLoft\Test\Api\Data\SalesInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(/** @noinspection PhpUndefinedClassInspection */
        SalesInformationExtensionInterface $extensionAttributes);
}
