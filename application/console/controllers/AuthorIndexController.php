<?php
class AuthorIndexController extends App_Console_Controller_Action_Abstract
{
    /**
     *
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process()
    {
        echo "Author names index update...\n";

        $this->addNewNames();

        echo "Author index build end...\n";
    }

    public function addNewNames()
    {
        echo "Add new names and update existing... ";
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('SELECT lib_author_id, a.`name`, '
             .     'GROUP_CONCAT(n.word SEPARATOR \' \') as `index` '
             . 'FROM lib_author a '
             . 'LEFT JOIN lib_author_name_index n USING (lib_author_id) '
             . 'GROUP BY lib_author_id');
        var_dump($res);
        echo "Done.\n";
    }

    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName()
    {
        return 'author_index';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName()
    {
        return 'a';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Update authors name index';
    }
}
?>