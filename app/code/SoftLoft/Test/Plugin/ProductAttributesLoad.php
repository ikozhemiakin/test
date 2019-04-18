<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Plugin;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductExtensionFactory;

/**
 * Class ProductAttributesLoad
 * @package SoftLoft\Test\Plugin
 */
class ProductAttributesLoad
{
    /**
     * @var ProductExtensionFactory
     */
    private $_extensionFactory;

    /**
     * @param ProductExtensionFactory $extensionFactory
     */
    public function __construct(ProductExtensionFactory $extensionFactory)
    {
        $this->_extensionFactory = $extensionFactory;
    }

    /**
     * Loads product entity extension attributes
     *
     * @param ProductInterface $entity
     * @param ProductExtensionInterface|null $extension
     * @return ProductExtensionInterface
     */
    public function afterGetExtensionAttributes(
        ProductInterface $entity,
        ProductExtensionInterface $extension = null
    ) {
        if ($extension === null) {
            $extension = $this->_extensionFactory->create();
        }

        return $extension;
    }
}