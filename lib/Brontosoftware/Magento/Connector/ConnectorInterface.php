<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Connector/ConnectorInterface.php
 */

interface Brontosoftware_Magento_Connector_ConnectorInterface
{
    /**
     * Creates a scope tree for the registration
     *
     * @param Brontosoftware_Connector_Model_RegistrationInterface $model
     * @return array
     */
    public function scopeTree(Brontosoftware_Magento_Connector_RegistrationInterface $model);

    /**
     * Creates a discovery object for the registration
     *
     * @param Brontosoftware_Connector_Model_RegistrationInterface $model
     * @return array
     */
    public function discovery(Brontosoftware_Magento_Connector_RegistrationInterface $model);

    /**
     * Creates an endpoint object for the registration and service
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param string $serviceName
     * @return array
     */
    public function endpoint(Brontosoftware_Magento_Connector_RegistrationInterface $model, $serviceName);

    /**
     * Performs an immediate script execution for the Brontosoftware_Magento_Connector_Middleware
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param array $script
     * @return array
     */
    public function executeScript(Brontosoftware_Magento_Connector_RegistrationInterface $model, $script);

    /**
     * Performs an immediate source lookup from the Brontosoftware_Magento_Connector_Middleware
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param string $sourceId
     * @param array $params
     * @return array
     */
    public function source(Brontosoftware_Magento_Connector_RegistrationInterface $model, $sourceId, $params = array());

    /**
     * Syncs the stored settings from connector
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @return array
     */
    public function settings(Brontosoftware_Magento_Connector_RegistrationInterface $model);

    /**
     * Sorts and flattens out any settings annotated with a sort_order
     *
     * @param array $settings
     * @return array
     */
    public function sortAndSet(array $settings);
}
