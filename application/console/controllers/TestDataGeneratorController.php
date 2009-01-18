<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Generating test data
 */
class TestDataGeneratorController extends App_Console_Controller_Action_Abstract
{
    const AUTHORS_COUNT = 512;

    const TITLES_COUNT = 32;

    const USERS_COUNT = 1024;

    const MARKS_PERCENT = 20;

    protected $_titles;

    protected $_users;

    /**
     *
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Generate test data...\n";

        try {
            $this->generateAuthorsAndBooks();
            $this->generateUsers();
            $this->generateMarks();
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "Test data generated...\n";
    }

    protected function generateAuthorsAndBooks()
    {
        $this->_titles = array();
        for ($authorNum = 0; $authorNum < self::AUTHORS_COUNT; ++ $authorNum) {
            $this->_titles[$authorNum] = array();

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
                $this->_titles[$authorNum][$titleNum] = $title;
                unset($title);
            }

            unset($author);
            echo "\r" . ($authorNum + 1) . " of " . self::AUTHORS_COUNT . " authors generated";
        }
        echo "\n";
    }

    protected function generateUsers()
    {
        $userFactory = App_User_Factory::getInstance();
        $this->_users = array();
        for ($i = 0; $i < self::USERS_COUNT; ++ $i) {
            $login = 'user' . $i;
            $user = $userFactory->getUserByLogin($login);
            if ($user === null) {
                $user = $userFactory->registerUser(array(
                    'login' => $login ,
                    'password' => 'lipton',
                    'email' => $login . '@librarian'
                ));
            }
            $this->_users[$i] = $user;

            echo "\r" . ($i + 1) . " of " . self::USERS_COUNT . " users generated";
        }

        echo "\n";
    }

    protected function generateMarks()
    {
        $marksCount = (int)(self::AUTHORS_COUNT * self::TITLES_COUNT
            * self::USERS_COUNT * self::MARKS_PERCENT / 100);

        for ($i = 0; $i < $marksCount; ++$i) {
            $user = $this->_users[rand(0, self::USERS_COUNT - 1)];
            $user->getBookshelf()
                ->setMark($this->_titles[rand(0, self::AUTHORS_COUNT - 1)][rand(0, self::TITLES_COUNT - 1)],
                rand(1, 5) - 3);
            if ($i % 100 == 0) {
                echo "\r" . ($i + 1) . " of " . $marksCount . " marks generated";
            }
        }

        echo "\n";
    }

    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName ()
    {
        return 'generate-test-data';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName ()
    {
        return 't';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription ()
    {
        return 'Generate data for testing purposes';
    }
}