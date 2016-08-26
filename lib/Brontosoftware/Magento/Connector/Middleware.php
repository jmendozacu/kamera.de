<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Connector/Middleware.php
 */

class Brontosoftware_Magento_Connector_Middleware implements Brontosoftware_Magento_Connector_MiddlewareInterface, Brontosoftware_Magento_Connector_ConnectorInterface
{
    const BASE_URL = 'https://middleware.brontops.com';
    const MDLWE_REGISTER = "/register/magento/{siteId}";
    const MDLWE_DEREGISTER = "/unregister/magento/{siteId}";
    const MDLWE_SETTINGS = "/settings/magento/{siteId}";
    const MDLWE_SCRIPT = "/connector/scripts";
    const TZ_CONFIG = 'general/locale/timezone';
    const XAUTHORIZATION = "X-Authorization";
    const USE_CUSTOM_ADMIN = "admin/url/use_custom";
    const CUSTOM_ADMIN_URL = "admin/url/custom";
    const USE_CUSTOM_PATH = "admin/url/use_custom_path";
    const CUSTOM_ADMIN_PATH = "admin/url/custom_path";

    protected $_client;
    protected $_encoder;
    protected $_logger;
    protected $_meta;
    protected $_encrypt;
    protected $_storeManager;
    protected $_eventManager;
    protected $_config;
    protected $_settings;

    /**
     * @param Brontosoftware_Transfer_Adapter $client
     * @param Brontosoftware_Serialize_BiDirectional $encoder
     * @param Brontosoftware_Magento_Core_Log_LoggerInterface $logger
     * @param Brontosoftware_Magento_Core_MetaInterface $meta
     * @param Brontosoftware_Magento_Core_EncryptorInterface $encrypt
     * @param Brontosoftware_Magento_Core_Event_ManagerInterface $eventManager
     * @param Brontosoftware_Magento_Core_Store_ManagerInterface $storeManager
     * @param Brontosoftware_Magento_Core_Config_ManagerInterface $config
     * @param Brontosoftware_Magento_Connector_SettingsInterface $settings
     */
    public function __construct(
        Brontosoftware_Transfer_Adapter $client,
        Brontosoftware_Serialize_BiDirectional $encoder,
        Brontosoftware_Magento_Core_Log_LoggerInterface $logger,
        Brontosoftware_Magento_Core_MetaInterface $meta,
        Brontosoftware_Magento_Core_EncryptorInterface $encrypt,
        Brontosoftware_Magento_Core_Event_ManagerInterface $eventManager,
        Brontosoftware_Magento_Core_Store_ManagerInterface $storeManager,
        Brontosoftware_Magento_Core_Config_ManagerInterface $config,
        Brontosoftware_Magento_Connector_SettingsInterface $settings
    ) {
        $this->_client = $client;
        $this->_encoder = $encoder;
        $this->_logger = $logger;
        $this->_meta = $meta;
        $this->_encrypt = $encrypt;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_settings = $settings;
    }

    /////////////////////////
    // Brontosoftware_Magento_Connector_MiddlewareInterface //
    /////////////////////////
    /**
     * @see parent
     */
    public function register(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        return $this->_postRegistration($model, self::BASE_URL . self::MDLWE_REGISTER);
    }

    /**
     * @see parent
     */
    public function deregister(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        if ($model->getIsActive()) {
            if ($this->_postRegistration($model, self::BASE_URL . self::MDLWE_DEREGISTER)) {
                try {
                    $this->_recursiveDelete($this->scopeTree($model));
                } catch (Exception $e) {
                    $this->_logger->critical($e);
                }
                return true;
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * @see parent
     */
    public function sync(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        try {
            $settings = $this->settings($model);
            $this->_walkSettings($model, $settings, array($this, '_saveSetting'));
            return true;
        } catch (Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }

    /**
     * @see parent
     */
    public function triggerFlush(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        try {
            $canFlush = !$this->_settings->isFlushDisabled($model->getScope(), $model->getScopeId());
            if ($canFlush && $model->getIsActive()) {
                $scriptUrl = self::BASE_URL . self::MDLWE_SCRIPT;
                $installKey = "{$model->getConnectorKey()}-magento-{$model->getScopeHash()}-{$model->getEnvironment()}";
                $script = new Brontosoftware_Magento_Connector_Discovery_Script($model);
                $this->_eventManager->dispatch('brontosoftware_connector_trigger_flush', array(
                    'script' => $script
                ));
                foreach ($script->getJobs() as $jobInfo) {
                    $request = $this->_client->createRequest('POST', $scriptUrl)
                        ->header(self::XAUTHORIZATION, "Bearer {$installKey}")
                        ->header("Content-Type", $this->_encoder->getMimeType())
                        ->body($this->_encoder->encode($jobInfo));
                    $response = $request->respond();
                    if ($response->code() >= 300) {
                        $this->_logger->critical("Failed to send {$jobInfo['id']}");
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }

    /**
     * @see parent
     */
    public function installKey(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        return rawurlencode($this->_encrypt->encrypt($model->getScopeHash()));
    }

    /**
     * @see parent
     */
    public function defaultStoreId($scopeType = 'default', $scopeId = '0')
    {
        switch ($scopeType) {
        case 'default':
            // `true` here gets the system default website
            return $this->_storeManager
                ->getWebsite(true)
                ->getDefaultGroup()
                ->getDefaultStoreId();
        case 'websites':
        case 'website':
            return $this->_storeManager
                ->getWebsite($scopeId)
                ->getDefaultGroup()
                ->getDefaultStoreId();
        default:
            return $scopeId;
        }
    }

    /**
     * @see parent
     */
    public function storeScopes(Brontosoftware_Magento_Connector_RegistrationInterface $model, $includeSelf = false)
    {
        return $this->_storeScopes($model->getScope(), $model->getScopeId(), $includeSelf);
    }

    /////////////////////////
    //  Brontosoftware_Magento_Connector_ConnectorInterface //
    /////////////////////////
    /**
     * @see parent
     */
    public function scopeTree(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        switch ($model->getScope()) {
        case 'default':
            return $this->_convertInstall();
        case 'website':
            $website = $this->_storeManager->getWebsite($model->getScopeId());
            return $this->_convertWebsite($website);
        default:
            $store = $this->_storeManager->getStore($model->getScopeId());
            return $this->_convert('store', $store);
        }
    }

    /**
     * @see parent
     */
    public function discovery(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $defaultStore = $this->_defaultStore($model);
        $urlParts = parse_url($this->_baseUrl($model));
        return array(
            'extensionGroups' => $this->_gatherEndpoints($model),
            'instanceInfo' => array(
                'id' => $model->getScopeHash(),
                'name' => $model->getName(),
                'type' => $this->_meta->getName(),
                'extensionVersion' => $this->_meta->getExtensionVersion(),
                'platformVersion' => $this->_meta->getVersion() . ' (' . $this->_meta->getEdition() . ')',
                'features' => array( 'promotion' => true ),
                'properties' => array(
                    'type' => ucfirst(strtolower($model->getEnvironment())),
                    'hostname' => $urlParts['host'],
                    'timezone' => $defaultStore->getConfig(self::TZ_CONFIG)
                )
            )
        );
    }

    /**
     * @see parent
     */
    public function endpoint(Brontosoftware_Magento_Connector_RegistrationInterface $model, $serviceName)
    {
        $endpoint = new Brontosoftware_Magento_Connector_Discovery_Endpoint();
        $eventAreaName = "brontosoftware_connector_{$serviceName}_endpoint";
        $this->_eventManager->dispatch($eventAreaName, array(
            'endpoint' => $endpoint,
            'registration' => $model
        ));
        $this->_eventManager->dispatch("{$eventAreaName}_additional", array(
            'endpoint' => $endpoint,
            'registration' => $model
        ));
        $endpointData = $endpoint->getInformation();
        foreach ($endpointData as $key => $settings) {
            $value = $settings;
            if (!preg_match('/^type/', $key)) {
                $value = $this->sortAndSet($settings);
            }
            $endpointData[$key] = $value;
        }
        return $endpointData;
    }

    /**
     * @see parent
     */
    public function executeScript(Brontosoftware_Magento_Connector_RegistrationInterface $model, $script)
    {
        return $this->_executeInfo($model, 'script', $script);
    }

    /**
     * @see parent
     */
    public function source(Brontosoftware_Magento_Connector_RegistrationInterface $model, $sourceId, $params = array())
    {
        $source = new Brontosoftware_Magento_Connector_Discovery_Source($model);
        $eventAreaName = "brontosoftware_connector_source_$sourceId";
        // Hand off implementation to extension due to source variety
        $this->_eventManager->dispatch($eventAreaName, array(
            'source' => $source->setParams($params),
        ));
        return $source->getResults();
    }

    /**
     * @see parent
     */
    public function settings(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $request = $this->_postAuthForm($model, self::BASE_URL . self::MDLWE_SETTINGS);
        $response = $request->respond();
        if ($response->code() >= 300) {
            throw new Brontosoftware_Transfer_Exception("Failed to retrieve settings.", $response->code(), $request);
        }
        return $this->_encoder->decode($response->body());
    }

    /**
     * @see parent
     */
    public function sortAndSet(array $settings)
    {
        usort($settings, array($this, '_applySortOrder'));
        return array_map(array($this, '_flattenSetting'), $settings);
    }

    ////////////////////////////
    // protected Helper Methods //
    ////////////////////////////
    /**
     * Executes either a Middleware script or job execution
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param string $name
     * @param array $object
     * @return array
     */
    protected function _executeInfo(Brontosoftware_Magento_Connector_RegistrationInterface $model, $name, $object)
    {
        $execution = new Brontosoftware_Magento_Connector_Discovery_Execution($model);
        $objectId = "{$object['extensionId']}_{$object["id"]}";
        $this->_eventManager->dispatch("brontosoftware_connector_{$name}_{$objectId}", array(
            $name => $execution->setObject($object)
        ));
        return $execution->getResults();
    }

    /**
     * Gathers all of the available extension groups available on the server
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @return array
     */
    protected function _gatherEndpoints(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $discovery = new Brontosoftware_Magento_Connector_Discovery();
        $this->_eventManager->dispatch("brontosoftware_connector_gather_endpoints", array(
            'discovery' => $discovery,
            'registration' => $model
        ));
        return $this->sortAndSet($discovery->getGroups());
    }

    /**
     * Applies the sort_order field on the various documents
     *
     * @param array $groupA
     * @param array $groupB
     * @return int
     */
    protected function _applySortOrder($groupA, $groupB)
    {
        if ($groupA['sort_order'] == $groupB['sort_order']) {
            return 0;
        }
        return ($groupA['sort_order'] < $groupB['sort_order']) ? -1 : 1;
    }

    /**
     * Performs the client transfer of registration material
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param string $baseUrl
     * @return boolean
     */
    protected function _postRegistration(Brontosoftware_Magento_Connector_RegistrationInterface $model, $baseUrl)
    {
        try {
            return $this->_postAuthForm($model, $baseUrl)->respond()->code() < 300;
        } catch (Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }

    /**
     * Performs the client transfer of registration auth material
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @param string $baseUrl
     * @return Brontosoftware_Transfer_Response
     */
    protected function _postAuthForm(Brontosoftware_Magento_Connector_RegistrationInterface $model, $baseUrl)
    {
        $baseUrl = str_replace('{siteId}', $model->getConnectorKey(), $baseUrl);
        return $this->_client->createRequest('POST', $baseUrl)
            ->header('Content-Type', $this->_encoder->getMimeType())
            ->body($this->_encoder->encode($this->_authForm($model)));
    }

    /**
     * Gets the authForm for middleware management
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @return array
     */
    protected function _authForm(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $credentials = array($this->installKey($model));
        if ($model->getIsProtected()) {
            $credentials[] = $model->getUsername();
            $credentials[] = $model->getPassword();
        }
        return array(
            'authType' => 'BASIC',
            'instanceType' => $model->getEnvironment(),
            'accountId' => $model->getScopeHash(),
            'username' => $this->_baseUrl($model),
            'password' => implode('::', $credentials)
        );
    }

    /**
     * Gets the full pingback URL for discovery
     *
     * @param Brontosoftware_Magento_Connector_RegistrationInterface $model
     * @return string
     */
    protected function _baseUrl(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $defaultStore = $this->_defaultStore($model);
        $customUrl = (bool)$defaultStore->getConfig(self::USE_CUSTOM_ADMIN);
        if ($customUrl) {
            $baseUrl = $defaultStore->getConfig(self::CUSTOM_ADMIN_URL);
        } else {
            $baseUrl = $defaultStore->getBaseUrl();
        }
        $customPath = (bool)$defaultStore->getConfig(self::USE_CUSTOM_PATH);
        if ($customPath) {
            $area = $defaultStore->getConfig(self::CUSTOM_ADMIN_PATH);
        } else {
            $area = $this->_meta->getAdminFrontName();
        }
        $suffix = $model->getPlatformSuffix();
        if (!empty($suffix)) {
            $suffix = trim($suffix, '/') . '/';
        }
        return $baseUrl . $area . '/' . $suffix;
    }

    /**
     * Gets the default store to handle frontend interaction
     *
     * @param Brontosoftware_Connector_Model_RegistrationInterface $model
     * @return mixed
     */
    protected function _defaultStore(Brontosoftware_Magento_Connector_RegistrationInterface $model)
    {
        $storeId = $this->defaultStoreId($model->getScope(), $model->getScopeId());
        return is_null($storeId) ? null : $this->_storeManager->getStore($storeId);
    }

    /**
     * Flattens the document setting
     *
     * @param array $setting
     * @return array
     */
    protected function _flattenSetting($setting)
    {
        return $setting['definition'];
    }

    /**
     * Converts the system install into a scope object
     *
     * @return array
     */
    protected function _convertInstall()
    {
        $tree = array(
            'id' => 'default.0',
            'name' => 'Default',
            'children' => array()
        );
        foreach ($this->_storeManager->getWebsites() as $website) {
            $tree['children'][] = $this->_convertWebsite($website);
        }
        return $tree;
    }

    /**
     * Converts a website into a scope object
     *
     * @param mixed $website
     * @return array
     */
    protected function _convertWebsite($website)
    {
        $tree = $this->_convert('website', $website);
        foreach ($website->getStores() as $store) {
            $tree['children'][] = $this->_convert('store', $store);
        }
        return $tree;
    }

    /**
     * Converts a store or website model into a scope object
     *
     * @param string $type
     * @param mixed $model
     * @return array
     */
    protected function _convert($type, $model)
    {
        return array(
            'id' => "{$type}.{$model->getId()}",
            'name' => $model->getName(),
            'children' => array()
        );
    }

    /**
     * Creates a list of store scopes from the root scope
     *
     * @param string $scopeName
     * @param mixed $scopeId
     * @param boolean $includeSelf
     * @return array
     */
    protected function _storeScopes($scopeName, $scopeId, $includeSelf = false)
    {
        $scopes = array();
        $stores = array();
        $defaultStoreId = $this->defaultStoreId($scopeName, $scopeId);
        switch ($scopeName) {
        case 'stores':
        case 'store':
            $stores[] = $this->_storeManager->getStore($scopeId);
            break;
        case 'website':
        case 'websites':
            $website = $this->_storeManager->getWebsite($scopeId);
            $stores = $website->getStores();
            break;
        default:
            $stores = $this->_storeManager->getStores();
        }
        foreach ($stores as $store) {
            if (!$includeSelf && $defaultStoreId == $store->getId()) {
                continue;
            }
            $scopes[$store->getCode()] = $store->getId();
        }
        return $scopes;
    }

    /**
     * Walk the connector settings and clear out all of the fields
     *
     * @param Brontosoftware_Connector_Model_Registration $model
     * @param array $settings
     * @param callable $callback
     */
    protected function _walkSettings($model, $settings, $callback)
    {
        $scopeName = $model->getScope();
        if ($scopeName != 'default') {
            $scopeName .= 's';
        }
        $this->_recursiveDelete($this->scopeTree($model));
        foreach ($settings as $general => $value) {
            $path = "brontosoftware/general/settings/{$general}";
            if ($general == 'featureMap') {
                foreach ($value as $feature => $flag) {
                    if (preg_match('/^magento_(.+)/', $feature, $matches)) {
                        $path = "brontosoftware/toggle/{$matches[1]}";
                    } else {
                        $path = "brontosoftware/general/features/{$feature}";
                    }
                    call_user_func($callback, $path, (int) $flag, $scopeName, $model->getScopeId());
                }
            } else if ($general == 'siteId' || $general == 'maskId') {
                call_user_func($callback, $path, $value, $scopeName, $model->getScopeId());
            }
        }

        $sections = array('extensions', 'objects');
        $scopedSettings = $settings['settings'];
        usort($scopedSettings, array($this, '_sortScopedSettings'));
        foreach ($scopedSettings as $scope) {
            list($sName, $sId) = explode('.', $scope['scope']);
            $stores = array();
            switch ($sName) {
            case 'default':
                break;
            case 'website':
                $stores = $this->_storeScopes($sName, $sId, true);
            default:
                $sName .= 's';
            }
            foreach ($scope['groups'] as $endpoint) {
                foreach ($sections as $section) {
                    $this->_sectionWalk($section, $sName, $sId, $endpoint, $callback);
                    foreach ($stores as $storeId) {
                        $this->_sectionWalk($section, 'stores', $storeId, $endpoint, $callback);
                    }
                }
                $eventAfter = "brontosoftware_{$endpoint['id']}_setting_sync_after";
                $this->_eventManager->dispatch($eventAfter, array(
                    'settings' => $endpoint,
                    'scope_name' => $sName,
                    'scope_id' => $sId,
                    'children' => $stores
                ));
            }
        }
        $this->_config->reinit();
        $this->_storeManager->reinitStores();
    }

    /**
     * Saves the scoped sections for the endpoint
     *
     * @param string $section
     * @param string $scopeName
     * @param int $scopeId
     * @param array $endpoint
     * @param callable $callback
     * @return void
     */
    protected function _sectionWalk($section, $scopeName, $scopeId, $endpoint, $callback)
    {
        foreach ($endpoint[$section] as $object) {
            foreach ($object['fields'] as $field) {
                if ($section == 'objects') {
                    $field['value']['id'] = $field['id'];
                    $field['value'] = serialize($field['value']);
                    $field['id'] = 'object_' . preg_replace('|[\-\s]|', '', $field['id']);
                } else if (is_bool($field['value'])) {
                    $field['value'] = (int)($field['value']);
                } else if (is_array($field['value'])) {
                    $field['value'] = implode(',', $field['value']);
                }
                $path = "brontosoftware/{$endpoint['id']}/{$section}/{$object['id']}/{$field['id']}";
                call_user_func($callback, $path, $field['value'], $scopeName, $scopeId);
            }
        }
    }

    /**
     * Saves the setting directly into the DB
     *
     * @param string $path
     * @param mixed $value
     * @param string $scopeName
     * @param mixed $scopeId
     * @return void
     */
    protected function _saveSetting($path, $value, $scopeName, $scopeId)
    {
        $this->_config->save($path, $value, $scopeName, $scopeId);
    }

    /**
     * @param array $tree
     * @param string $suffix
     * @return void
     */
    protected function _recursiveDelete($tree, $suffix = '')
    {
        list($scopeName, $scopeId) = explode('.', $tree['id']);
        $this->_config->deleteAll('brontosoftware/' . $suffix, $scopeName, $scopeId);
        foreach ($tree['children'] as $child) {
            $this->_recursiveDelete($child, $suffix);
        }
    }

    /**
     * User defined sort on settings to guarantee the scope order
     *
     * @param array $scopeA
     * @param array $scopeB
     * @return int
     */
    protected function _sortScopedSettings($scopeA, $scopeB)
    {
        preg_match('/([^\.])+/', $scopeA['scope'], $matchA);
        preg_match('/([^\.])+/', $scopeB['scope'], $matchB);
        if ($matchA[1] == $matchB[1]) {
            return 0;
        } else if ($matchA[1] == 'website') {
            return $matchB[1] == 'default' ? 1 : -1;
        } else {
            return 1;
        }
    }
}