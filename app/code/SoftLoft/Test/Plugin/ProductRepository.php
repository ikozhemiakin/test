<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SoftLoft\Test\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ProductAttributesLoad
 * @package SoftLoft\Test\Plugin
 */
class ProductRepository
{
    /**
     * @var ProductExtensionFactory
     */
    private $extensionFactory;

    /**
     * @param ProductExtensionFactory $extensionFactory
     */
    public function __construct(ProductExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /** after get plugin
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $entity
     * @return ProductInterface
     */
    public function afterGet
    (
        ProductRepositoryInterface $subject,
        ProductInterface $entity
    )
    {
        $salesInformation = $this->customDataRepository->get($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        /** get current extension attributes from entity **/
        $extensionAttributes->setOurCustomData($salesInformation);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /** after get list plugin
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $entity
     * @return ProductInterface
     */
    public function afterGetList
    (
        ProductRepositoryInterface $subject,
        ProductInterface $entity
    )
    {
        $salesInformation = $this->customDataRepository->get($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes();
        /** get current extension attributes from entity **/
        $extensionAttributes->setOurCustomData($salesInformation);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * after save plugin
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param ProductInterface $entity
     * @return ProductInterface
     */
    public function afterSave
    (
        ProductRepositoryInterface $subject,
        ProductInterface $entity
    )
    {
        $extensionAttributes = $entity->getExtensionAttributes();
        /** get current extension attributes from entity **/
        $salesInformation = $extensionAttributes->getSalesInformation();
        $this->customDataRepository->save($salesInformation);

        return $entity;
    }
}