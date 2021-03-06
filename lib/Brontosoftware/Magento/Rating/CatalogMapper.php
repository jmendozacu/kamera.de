<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Magento/Rating/CatalogMapper.php
 */

class Brontosoftware_Magento_Rating_CatalogMapper extends Brontosoftware_Magento_Product_CatalogMapperAbstract
{
    private static $_codes = array(
        'last_created' => 'Last Review Created',
        'avg_rating' => 'Avg. Review Rating',
        'avg_rating_approved' => 'Avg. Approved Review Rating',
        'review_cnt' => 'Number of Reviews'
    );

    private static $_defaultCodes = array(
        'average_rating' => 'avg_rating',
        'review_count' => 'review_cnt'
    );

    private static $_typeCodes = array(
        'last_created' => 'date',
        'avg_rating' => 'double',
        'avg_rating_approved' => 'double',
        'review_cnt' => 'integer'
    );

    /**
     * @see parent
     */
    public function getExtraFields()
    {
        return self::$_codes;
    }

    /**
     * @see parent
     */
    public function getDefaultFields()
    {
        return self::$_defaultCodes;
    }

    /**
     * @see parent
     */
    public function getFieldAttributes()
    {
        return self::$_typeCodes;
    }
}
