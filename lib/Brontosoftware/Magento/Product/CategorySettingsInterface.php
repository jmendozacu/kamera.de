<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Product/CategorySettingsInterface.php
 */

interface Brontosoftware_Magento_Product_CategorySettingsInterface
{
    const XML_PATH_ENCAPSULATION = 'brontosoftware/product/extensions/settings/categoryBranchDelimiter';
    const XML_PATH_FORMAT = 'brontosoftware/product/extensions/settings/categoryFormat';
    const XML_PATH_DELIMITER = 'brontosoftware/product/extensions/settings/categoryDelimiter';
    const XML_PATH_SPECIFICITY = 'brontosoftware/product/extensions/settings/categorySpecificity';
    const XML_PATH_BROADNESS = 'brontosoftware/product/extensions/settings/categoryBroadness';

    /**
     * @param string $scope
     * @param mixed $scopeId
     * @return string
     */
    public function getCategoryEncapsulation($scope = 'default', $scopeId = null);

    /**
     * @param string $scope
     * @param mixed $scopeId
     * @return string
     */
    public function getCategoryDelimiter($scope = 'default', $scopeId = null);

    /**
     * @param string $scope
     * @param mixed $scopeId
     * @return string
     */
    public function getCategoryFormat($scope = 'default', $scopeId = null);

    /**
     * @param string $scope
     * @param mixed $scopeId
     * @return string
     */
    public function getCategorySpecificity($scope = 'default', $scopeId = null);

    /**
     * @param string $scope
     * @param mixed $scopeId
     * @return string
     */
    public function getCategoryBroadness($scope = 'default', $scopeId = null);

}
