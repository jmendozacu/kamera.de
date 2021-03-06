<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Recommendation/Collect/Context/Quote.php
 */


class Brontosoftware_Magento_Recommendation_Collect_Context_Quote extends Brontosoftware_Magento_Recommendation_Collect_ContextAbstract
{
    /**
     * @see parent
     */
    public function getContextType()
    {
        return self::TYPE_CART;
    }

    /**
     * @see parent
     */
    public function getContextId()
    {
        return $this->_item->getId();
    }
}
