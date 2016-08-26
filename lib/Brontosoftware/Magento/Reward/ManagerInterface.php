<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Reward/ManagerInterface.php
 */

interface Brontosoftware_Magento_Reward_ManagerInterface
{
    /**
     * Loads reward points for a given customer for a given website
     *
     * @param mixed $customerId
     * @param mixed $websiteId
     * @return mixed
     */
    public function getByCustomer($customerId, $websiteId);
}