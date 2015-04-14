<?php
/**
 * Category controller
 *
 * @category   Hardik
 * @package    Hardik_Ajaxnav
 */
require_once 'Mage/Catalog/controllers/CategoryController.php';

class Hardik_Ajaxnav_Catalog_CategoryController extends Mage_Catalog_CategoryController
{
    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());

            /* custom code start */
            //$isAjax = $this->getRequest()->getParam('ajax');
            if ($this->getRequest()->isXmlHttpRequest()) {
                $update->removeHandle('catalog_category_layered');
                $update->removeHandle('catalog_category_default');
                $update->addHandle('catalog_category_ajax');
                if ($category->getIsAnchor()) {
                    $update->addHandle('catalog_category_ajax_layered');
                } else {
                    $update->addHandle('catalog_category_ajax_default');
                }
            }
            /* custom code end */
            
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }
            
            $this->generateLayoutXml()->generateLayoutBlocks();
            
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
        elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
