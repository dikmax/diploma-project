<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

// TODO maybe this file is absolete

/**
 * Breadcrumbs action helper
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Controller_Action_Helper_Breadcrumbs extends Zend_Controller_Action_Helper_Abstract
{
     protected $_breadcrumbs = array();

     /**
      * Initialize breadcrumbs
      */
     public function init()
     {
         $this->_breadcrumbs[] = array("url" => "/", "name" => "Главная");
     }

     /**
      * Reinitialize breadcurmbs
      */
     public function clear()
     {
         $this->_breadcrumbs = array();
         $this->init();
     }
     
     /**
      * Adds node to breadcrumbs path
      *
      * @param string $url
      * @param string $name
      */
     public function addCrumb($url, $name)
     {
         $this->_breadcrumbs[] = array("url" => $url ? $url : '',
                                       "name" => $name ? $name : '');
     }
     
     
     /**
      * <code>direct()</code>: Perform helper when called as
      * <code>$this->_helper->Breadcrumbs($url, $name)</code>
      *
      * @param string $url
      * @param string $name
      */
     public function direct($url, $name)
     {
         $this->addCrumb($url, $name);
     }

     /**
      * Post-dispatch hook handler. Sets view variable <code>$breadcrumb</code>
      */
     public function postDispatch()
     {
         $this->getActionController()->view->breadcrumbs = $this->_breadcrumbs;
     }
}
