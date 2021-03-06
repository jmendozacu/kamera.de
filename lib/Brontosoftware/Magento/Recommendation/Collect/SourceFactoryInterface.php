<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Recommendation/Collect/SourceFactoryInterface.php
 */

interface Brontosoftware_Magento_Recommendation_Collect_SourceFactoryInterface
{
    /**
     * Creates a source, by name, with a promotion and context
     *
     * @param string $source
     * @param array $promotion
     * @param array $context
     * @return Brontosoftware_Magento_Recommendation_Collect_SourceIntercace
     */
    public function create($source, array $promotion, array $context = array());
}
