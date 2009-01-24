<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Update similar titles
 */
class SimilarTitlesUpdateController extends App_Console_Controller_Action_Abstract
{
    /**
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Update similar titles...\n";

        try {
            $this->updateSimilarTitles();
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "Similar titles updated...\n";
    }

    /**
     * Update similar titles
     */
    protected function updateSimilarTitles()
    {
        $maxId = App_Library::getMaxTitleId();
        for ($id = 1; $id <= $maxId; ++$id) {
            $title = App_Library_Title::getById($id);
            if ($title !== null) {
                echo "Update title " . $title->getName() . " (" . $id . " of " . $maxId . ")...\n";
                $title->updateSimilar();
            }
        }
    }

    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName()
    {
        return 'update-similar-titles';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName ()
    {
        return 'b';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription ()
    {
        return 'Update similar titles';
    }
}