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
 * App_Library class contains static methods for working with library
 */
class App_Library
{
    /**
     * Returns author by url part
     *
     * @param string $authorUrl Part of url, which contains author name
     * 
     * @return App_Library_Author
     * 
     * @throws App_Library_Exception
     * @throws App_Library_Exception_AuthorNotFound
     */
    public static function getAuthorByUrl($authorUrl)
    {
        if (!is_string($authorUrl)) {
            throw new App_Library_Exception('Author url must be a string');
        }
        
        $db = Zend_Registry::get('db');
        
        $row = $db->fetchRow('SELECT lib_author_id, name, url, description_text_id, '
            .     'front_description, lib_writeboard_id '
            . 'FROM lib_author '
            . 'WHERE url = :url', array(':url' => $authorUrl));
        
        if ($row === false) {
            throw new App_Library_Exception_AuthorNotFound('Author ' . $authorUrl . ' not found');
        }
        return new App_Library_Author($row);
    }
    
    /**
     * Returns title by url
     *
     * @param string|App_Library_Author $authorUrl part which contains author's name
     * @param string $titleUrl part which contains title 
     * 
     * @return App_Library_Title
     * 
     * @throws 
     */
    public static function getTitleByUrl($authorUrl, $titleUrl)
    {
        if (!is_string($titleUrl)) {
            throw new App_Library_Exception('Title url must be string');
        }
        
        if ($authorUrl instanceof App_Library_Author) {
            $author = $authorUrl;
        } else {
            $author = self::getAuthorByUrl($authorUrl);
        }
        $authorId = $author->getId();
        
        $db = Zend_Registry::get('db');
        $row = $db->fetchRow('SELECT t.lib_title_id, t.name, t.url, '
             .     't.authors_index, t.description_text_id, t.front_description, '
             .     't.lib_writeboard_id '
             . 'FROM lib_title t '
             . 'LEFT JOIN lib_author_has_title h USING (lib_title_id) '
             . 'WHERE h.lib_author_id = :lib_author_id AND t.url = :url',
             array(':lib_author_id' => $authorId,
                   ':url' => $titleUrl)
        );
        
        if ($row === false) {
            throw new App_Library_Exception_TitleNotFound('Title ' . $titleUrl . ' not found');
        }
        
        return new App_Library_Title($row);
    }
    
}