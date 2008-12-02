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
 * App_Library class contains static methods for working with library
 */
class App_Library
{
    /**
     * Returs list of most read authors
     *
     * @param int $count Count of authors
     * @return array
     */
    public static function getMostReadAuthors($count = 10)
    {
        if (!is_numeric($count)) {
            throw new App_Library_Exception('Count of authors must be integer');
        }
        $count = (int)$count;

        $db = Zend_Registry::get('db');

        $authors = $db->fetchAll('SELECT a.`lib_author_id`, a.`name`, '
                 .     'a.`description_text_id`, a.`front_description`, '
                 .     'a.`lib_writeboard_id`, count(lib_author_id) `count` '
                 . 'FROM lib_user_bookshelf b '
                 . 'LEFT JOIN lib_author_has_title t USING (lib_title_id) '
                 . 'LEFT JOIN lib_author a USING (lib_author_id) '
                 . 'GROUP BY lib_author_id '
                 . 'ORDER BY `count` DESC '
                 . 'LIMIT ' . $count);

        if ($authors === false) {
            return array();
        }

        $result = array();
        foreach ($authors as $author) {
            $result[] = new App_Library_Author($author);
        }

        return $result;
    }

    /**
     * Returs list of most read titles
     *
     * @param int $count Count of titles
     * @return array
     */
    public static function getMostReadTitles($count = 10)
    {
        if (!is_numeric($count)) {
            throw new App_Library_Exception('Count of titles must be integer');
        }
        $count = (int)$count;

        $db = Zend_Registry::get('db');

        $titles = $db->fetchAll('SELECT t.lib_title_id, t.name, '
                .     't.authors_index, t.description_text_id, t.front_description, '
                .     't.lib_writeboard_id, count(lib_title_id) `count` '
                . 'FROM lib_user_bookshelf b '
                . 'LEFT JOIN lib_title t USING (lib_title_id) '
                . 'GROUP BY lib_title_id '
                . 'ORDER BY `count` DESC '
                . 'LIMIT ' . $count);

        if ($titles === false) {
            return array();
        }

        $result = array();
        foreach ($titles as $title) {
            $result[] = new App_Library_Title($title);
        }

        return $result;
    }
}