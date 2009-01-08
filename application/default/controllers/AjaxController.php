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
    /**
     * Initializes ajax controller
     */
    public function init()
    {
        $this->initAjax();
    }

    /**
     * Set mark
     */
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

    /**
     * Remove mark
     */
    public function removeMarkAction()
    {
        $user = App_User_Factory::getSessionUser();
        if (!$user) {
            $this->fail();
            return;
        }
        $titleId = $this->_request->getParam('title_id');

        if (!is_numeric($titleId)) {
            $this->fail();
            return;
        }

        $user->getBookshelf()->removeMark($titleId);
        $this->success();
    }
}