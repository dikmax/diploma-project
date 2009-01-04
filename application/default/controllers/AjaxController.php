<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Controller for different ajax actions
 */
class AjaxController extends App_Controller_AjaxAction
{
    public function init()
    {
        $this->initAjax();
    }

    public function setMarkAction()
    {
        $user = App_User_Factory::getSessionUser();
        if (!$user) {
            $this->fail();
            return;
        }
        $titleId = $this->_request->getParam('title_id');
        $mark = $this->_request->getParam('mark');

        if (!is_numeric($titleId) || !is_numeric($mark)) {
            $this->fail();
            return;
        }

        $user->getBookshelf()->setMark($titleId, $mark);
        $this->success();
    }
}