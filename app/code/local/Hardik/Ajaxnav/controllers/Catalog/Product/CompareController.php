<?php
/**
 * Catalog comapare controller
 * referer urlescape after add to compare issue fix
 *
 * @category   Hardik
 * @package    Hardik_Ajaxnav
 */

require_once 'Mage/Catalog/controllers/Product/CompareController.php';

class Hardik_Ajaxnav_Catalog_Product_CompareController extends Mage_Catalog_Product_CompareController
{
    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl()
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }

        /* added fix */
        //$refererUrl = Mage::helper('core')->escapeUrl($refererUrl);

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }
}
