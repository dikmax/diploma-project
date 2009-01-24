<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Update similar authors
 */
class SimilarAuthorsUpdateController extends App_Console_Controller_Action_Abstract
{
    /**
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Update similar authors...\n";

        try {
            $this->updateSimilarAuthors();
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "Similar authors updated...\n";
    }

    /**
     * Update similar titles
     */
    protected function updateSimilarAuthors()
    {
        $maxId = App_Library::getMaxAuthorId();
        for ($id = 1; $id <= $maxId; ++$id) {
            $author = App_Library_Author::getById($id);
            if ($author !== null) {
                echo "Update title " . $author->getName() . " (" . $id . " of " . $maxId . ")...\n";
                $author->updateSimilar();
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
        return 'update-similar-authors';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName ()
    {
        return '';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription ()
    {
        return 'Update similar authors';
    }
}