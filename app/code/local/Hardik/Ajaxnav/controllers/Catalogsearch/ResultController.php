<?php
/**
 * Catalog Search controller
 *
 * @category   Hardik
 * @package    Hardik_Ajaxnav
 */
require_once 'Mage/CatalogSearch/controllers/ResultController.php';

class Hardik_Ajaxnav_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController
{
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
                else {
                    $query->prepare();
                }
            }

            Mage::helper('catalogsearch')->checkNotes();

            /* custom code start */
            //$isAjax = $this->getRequest()->getParam('ajax');
            if ($this->getRequest()->isXmlHttpRequest()) {
                $update = $this->getLayout()->getUpdate();
                $update->removeHandle('catalogsearch_result_index');
                $update->addHandle('catalogsearch_result_ajax_index');
                
                // set unique cache ID to bypass caching
                $cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $update->getHandles()));
                $update->setCacheId($cacheId);

                $this->loadLayoutUpdates();
                $this->generateLayoutXml()->generateLayoutBlocks();
            } else {
                $this->loadLayout();
            }
            /* custom code end */
            
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
            $this->_redirectReferer();
        }
    }
}
