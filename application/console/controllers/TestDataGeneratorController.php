<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Generating test data
 */
class TestDataGeneratorController extends App_Console_Controller_Action_Abstract
{
    const AUTHORS_COUNT = 128;

    const TITLES_COUNT = 32;

    /**
     *
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Generate test data...\n";

        try {
            $this->generateAuthorsAndBooks();
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "Test data generated...\n";
    }

    protected function generateAuthorsAndBooks()
    {
        for ($authorNum = 0; $authorNum < self::AUTHORS_COUNT; ++$authorNum) {
            $authorName = 'test-author-' . ($authorNum + 1);
            $author = App_Library_Author::getByName($authorName);

            if ($author === null) {
                // New author: creating
                $author = new App_Library_Author(array(
                    'name' => $authorName,
                ));

                $author->write();
            }

            for ($titleNum = 0; $titleNum < self::TITLES_COUNT; ++$titleNum) {
                $titleName = 'test-title-' . ($titleNum + 1);

                $title = App_Library_Title::getByName($author, $titleName);

                if ($title === null) {
                    // New title: creating
                    $title = new App_Library_Title(array(
                        'name' => $titleName,
                        'authors' => array($author),
                        'authors_index' => $author->getName()
                    ));

                    $title->write();
                }

                unset($title);
            }

            unset($author);

            if (($authorNum & 31) == 0) {
                echo ($authorNum + 1) . " authors generated\n";
            }
        }

        echo self::AUTHORS_COUNT . " authors generated\n";
    }
    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName()
    {
        return 'generate-test-data';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName()
    {
        return 't';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Generate data for testing purposes';
    }
}