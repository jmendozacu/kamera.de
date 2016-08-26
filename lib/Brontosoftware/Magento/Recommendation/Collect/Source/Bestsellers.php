<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Recommendation/Collect/Source/Bestsellers.php
 */


class Brontosoftware_Magento_Recommendation_Collect_Source_Bestsellers implements Brontosoftware_Magento_Recommendation_Collect_SourceInterface
{
    protected $_promotion;
    protected $_factory;

    /**
     * @param array $promotion
     * @param Brontosoftware_Magento_Core_Report_CollectionFactoryInterface $factory
     */
    public function __construct(
        $promotion,
        Brontosoftware_Magento_Core_Report_CollectionFactoryInterface $factory
    ) {
        $this->_promotion = $promotion;
        $this->_factory = $factory;
    }

    /**
     * @see parent
     */
    public function collect(Brontosoftware_Magento_Recommendation_Collect_ContextInterface $context)
    {
        $fromDate = strtotime('-' . $this->_promotion['threshold'] . ' days');
        $excluded = array_keys($context->getContextItems());
        $best = $this->_factory->getBestSellers()
            ->setPeriod('day')
            ->addStoreFilter(array($context->getStoreId()))
            ->setDateRange(date('Y-m-d', $fromDate), date('Y-m-d'))
            ->setPageSize($this->_promotion['limit'])
            ->setAggregatedColumns(array('qty_ordered' => "sum(qty_ordered)"))
            ->setOrder('qty_ordered', 'DESC');
        if (!empty($excluded)) {
            $best->addFieldToFilter('product_id', array('nin' => $excluded));
        }
        $items = array();
        foreach ($best as $event) {
            $items[] = $event->getProductId();
        }
        return $items;
    }
}