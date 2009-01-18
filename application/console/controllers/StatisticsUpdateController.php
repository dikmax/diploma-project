<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Update statistical information
 */
class StatisticsUpdateController extends App_Console_Controller_Action_Abstract
{
    /**
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Update statistical information...\n";

        try {
            $this->updateNeighbors();
            $this->updateSuggestedBooks();
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "Statistical information updated...\n";
    }

    /**
     * Update lists of neighbors
     */
    protected function updateNeighbors()
    {
        $userFactory = App_User_Factory::getInstance();

        $maxId = $userFactory->getMaxUserId();

        for ($id = 1; $id <= $maxId; ++$id) {
            $user = $userFactory->getUser($id);
            if ($user !== null) {
                echo "Update user " . $user->getLogin() . "(" . $id . " of " . $maxId . ")...\n";
                $user->getNeighbors()->updateNeighborsList();
            }
        }
    }

    /**
     * Update lists of suggested books
     */
    protected function updateSuggestedBooks()
    {
        $userFactory = App_User_Factory::getInstance();

        $maxId = $userFactory->getMaxUserId();

        for ($id = 1; $id <= $maxId; ++$id) {
            $user = $userFactory->getUser($id);
            if ($user !== null) {
                echo "Update suggested books for " . $user->getLogin() . "(" . $id . " of " . $maxId . ")...\n";
                $user->getBookshelf()->updateSuggestedBooks();
            }
        }
    }

    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName ()
    {
        return 'update-statistics';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName ()
    {
        return 's';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription ()
    {
        return 'Update statistical information';
    }
}