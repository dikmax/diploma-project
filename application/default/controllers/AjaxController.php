<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Controller for different ajax actions
 */
class AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function authorSuggestAction()
    {
        //$query = $this->getRequest()->getParam('query');

        $response = new Zend_Dojo_Data('author_id', array(
            array(
                "author_id" => "1",
                "name" => "Клиффорд Саймак"
            ),
            array(
                "author_id" => "2",
                "name" => "Клчтототам Сайтоже"
            )
        ));

        echo $response;
    }
}