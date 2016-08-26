<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Integration/SettingsInterface.php
 */

interface Brontosoftware_Magento_Integration_SettingsInterface extends Brontosoftware_Magento_Integration_CartSettingsInterface, Brontosoftware_Magento_Integration_PopupSettingsInterface, Brontosoftware_Magento_Integration_CouponSettingsInterface
{
    /**
     * Creates a random guid
     *
     * @return string
     */
    public function generateUUID();
}