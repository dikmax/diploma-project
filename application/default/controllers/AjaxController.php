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
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'text/json-comment-filtered');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setScriptAction('json-comment-filtered');
    }

    public function authorSuggestAction()
    {
        //$query = $this->getRequest()->getParam('query');

        $res = new Zend_Dojo_Data('author_id', array(
            array(
                "author_id" => "1",
                "name" => "Клиффорд Саймак"
            ),
            array(
                "author_id" => "2",
                "name" => "Клчтототам Сайтоже"
            )
        ));

        $this->view->data = $res;
    }
}