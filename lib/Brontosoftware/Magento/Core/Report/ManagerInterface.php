<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Core/Report/ManagerInterface.php
 */

interface Brontosoftware_Magento_Core_Report_ManagerInterface
{
    /**
     * Determines if the report key in question corresponds to a report collection
     *
     * @param string $reportKey
     * @return boolean
     */
    public function isReportKey($reportKey);

    /**
     * Gets the time at which the report was aggregated
     *
     * @param string $reportKey
     * @return string
     */
    public function getLastUpdate($reportKey);

    /**
     * Refreshes the report collection within the specified range
     *
     * @param string $reportKey
     * @param mixed $fromTime
     * @param mixed $toTime
     * @return boolean
     */
    public function refresh($reportKey, $fromTime = null, $toTime = null);
}