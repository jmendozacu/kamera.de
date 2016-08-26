<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Core/Catalog/CategoryResolverFactoryInterface.php
 */

interface Brontosoftware_Magento_Core_Catalog_CategoryResolverFactoryInterface
{
    /**
     * Creates a category resolver from a product
     *
     * @param string $resolver
     * @param mixed $product
     * @return Brontosoftware_Magento_Core_Catalog_CategoryResolverInterface
     */
    public function create($resolver, $product);
}