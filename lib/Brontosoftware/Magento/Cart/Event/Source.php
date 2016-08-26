<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Cart/Event/Source.php
 */

class Brontosoftware_Magento_Cart_Event_Source extends Brontosoftware_Magento_Order_Event_CartBasedSourceAbstract
{
    protected $_settings;

    /**
     * @param Brontosoftware_Magento_Cart_SettingsInterface $settings
     * @param Brontosoftware_Magento_Connector_SettingsInterface $connector
     * @param Brontosoftware_Magento_Order_SettingsInterface $helper
     * @param Brontosoftware_Magento_Core_Cookie_ReaderInterface $cookie
     */
    public function __construct(
        Brontosoftware_Magento_Cart_SettingsInterface $settings,
        Brontosoftware_Magento_Connector_SettingsInterface $connector,
        Brontosoftware_Magento_Order_SettingsInterface $helper,
        Brontosoftware_Magento_Core_Cookie_ReaderInterface $cookie
    ) {
        parent::__construct($connector, $helper, $cookie);
        $this->_settings = $settings;
    }

    /**
     * @see parent
     */
    public function create($quote)
    {
        $action = $this->action($quote);
        return array(
            'tid' => $this->_readCookie($quote),
            'is_new' => $quote->isObjectNew(),
            'is_deleted' => $quote->isDeleted(),
            'redirect_url' => $this->_settings->getRedirectUrl($quote->getId(), $quote->getStore()),
            'emailAddress' => $this->_settings->getCartRecoveryEmail($quote),
            'uniqueKey' => implode('.', array(
                $this->getEventType(),
                $action[0],
                $this->_status($quote),
                $quote->getId()
            ))
        );
    }

    /**
     * @see parent
     */
    public function action($quote)
    {
        if (!$quote->getId()) {
            return '';
        } else {
            return $quote->isDeleted() || $quote->getItemsCount() == 0 ?
                'delete' :
                'replace';
        }
    }

    /**
     * @see parent
     */
    public function getEventType()
    {
        return 'cart';
    }

    /**
     * Override for delete short circuits
     *
     * @see parent
     */
    public function transform($quote)
    {
        if ($quote->isDeleted()) {
            return array(
                'customerCartId' => $quote->getId(),
                'status' => $this->_status($quote)
            );
        } else {
            return parent::transform($quote);
        }
    }

    /**
     * @see parent
     */
    protected function _initializeData($quote, $isBase)
    {
        $data = array(
            'emailAddress' => $this->_settings->getCartRecoveryEmail($quote),
            'customerCartId' => $quote->getId(),
            'url' => $quote->hasRedirectUrl() ?
                $quote->getRedirectUrl() :
                $this->_settings->getRedirectUrl($quote->getId(), $quote->getStore()),
            'status' => $this->_status($quote),
            'phase' => $this->_phase($quote),
            'currency' => $isBase ?
                $quote->getBaseCurrencyCode() :
                $quote->getQuoteCurrencyCode(),
        );
        return $data;
    }

    /**
     * Returns the desired cart phase based on quote info
     *
     * @param mixed $quote
     * @return string
     */
    protected function _status($quote)
    {
        if ($quote->isDeleted()) {
            return 'EXPIRED';
        }
        if ($quote->getReservedOrderId()) {
            return 'COMPLETE';
        } else if ($quote->isObjectNew() || $quote->getIsActive()) {
            return 'ACTIVE';
        } else {
            return 'EXPIRED';
        }
    }

    /**
     * Returns the appropriate cart phase based on the quote info
     *
     * @param mixed $quote
     * @return string
     */
    protected function _phase($quote)
    {
        $phase = 'SHOPPING';
        if ($quote->getReservedOrderId()) {
            $phase = 'ORDER_COMPLETE';
        }
        return $phase;
    }
}