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
 * Bbcode markup model
 */
class App_Markup
{
    /**
     * @var StringParser_BBCode
     */
    protected static $_markup = null;

    /**
     * Initializes markup renderer
     */
    protected static function initRenderer()
    {
        if (self::$_markup === null) {
            require_once 'BBCode/stringparser.class.php';
            require_once 'BBCode/stringparser_bbcode.class.php';

            self::$_markup = new StringParser_BBCode();
            self::$_markup->addFilter(STRINGPARSER_FILTER_PRE, array('App_Markup', 'convertLineBreaks'));

            self::$_markup->addParser(array('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
            self::$_markup->addParser(array('block', 'inline', 'link', 'listitem'), 'nl2br');
            self::$_markup->addParser('list', array('App_Markup', 'stripContents'));

            // Simple markup
            self::$_markup->addCode('b', 'simple_replace', null,
                array('start_tag' => '<b>', 'end_tag' => '</b>'),
                'inline', array ('listitem', 'block', 'inline', 'link'), array ());
            self::$_markup->addCode('i', 'simple_replace', null,
                array ('start_tag' => '<i>', 'end_tag' => '</i>'),
                'inline', array ('listitem', 'block', 'inline', 'link'), array ());

            // Links
            self::$_markup->addCode('url', 'usecontent?', array('App_Markup', 'processUrl'),
                array('usecontent_param' => 'default'),
                'link', array ('listitem', 'block', 'inline'), array ('link'));

            // Images
            self::$_markup->addCode('img', 'usecontent', array('App_Markup', 'processImage'),
                array(),
                'image', array('listitem', 'block', 'inline', 'link'), array());
            self::$_markup->setOccurrenceType ('img', 'image');
            self::$_markup->setMaxOccurrences ('image', 2);

            // Lists
            self::$_markup->addCode('list', 'simple_replace', null,
                array('start_tag' => '<ul>', 'end_tag' => '</ul>'),
                'list', array('block', 'listitem'), array());
            self::$_markup->addCode('*', 'simple_replace', null,
                array('start_tag' => '<li>', 'end_tag' => '</li>'),
                'listitem', array('list'), array());
            self::$_markup->setCodeFlag('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
            self::$_markup->setCodeFlag('*', 'paragraphs', true);
            self::$_markup->setCodeFlag('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
            self::$_markup->setCodeFlag('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
            self::$_markup->setCodeFlag('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

            // Site links
            self::$_markup->addCode('author', 'usecontent', array('App_Markup', 'processAuthor'),
                array(),
                'sitelink', array('listitem', 'block', 'inline'), array('link', 'sitelink'));
            self::$_markup->addCode('title', 'usecontent', array('App_Markup', 'processTitle'),
                array(),
                'sitelink', array('listitem', 'block', 'inline'), array('link', 'sitelink'));
            self::$_markup->addCode('user', 'usecontent', array('App_Markup', 'processUser'),
                array(),
                'sitelink', array('listitem', 'block', 'inline'), array('link', 'sitelink'));

            self::$_markup->setRootParagraphHandling (true);
        }
    }

    /**
     * Renders string
     *
     * @param string $string
     *
     * @return string
     */
    public static function render($string)
    {
        self::initRenderer();

        return self::$_markup->parse($string);
    }

    /*
     * Function used in parser
     */
    public static function convertLineBreaks($text) {
        return preg_replace ("/\015\012|\015|\012/", "\n", $text);
    }

    public static function stripContents($text) {
        return preg_replace ("/[^\n]/", '', $text);
    }

    public static function processUrl($action, $attributes, $content, $params, $node_object) {
        if (!isset ($attributes['default'])) {
            $url = $content;
            $text = htmlspecialchars($content);
        } else {
            $url = $attributes['default'];
            $text = $content;
        }
        if ($action == 'validate') {
            if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
            || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
                return false;
            }
            return true;
        }
        return '<a href="' . htmlspecialchars($url) . '">' . $text . '</a>';
    }

    public static function processImage($action, $attributes, $content, $params, $node_object) {
        if ($action == 'validate') {
            if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
            || substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') {
                return false;
            }
            return true;
        }
        return '<img src="'.htmlspecialchars($content).'" alt="" />';
    }

    public static function processAuthor($action, $attributes, $content, $params, $node_object) {
        if ($action == 'validate') {
            if (!$content) {
                return false;
            }
            return true;
        }
        return '<a href="/library/' . urlencode($content) . '">' . htmlspecialchars($content) . '</a>';
    }

    public static function processTitle($action, $attributes, $content, $params, $node_object) {
        if ($action == 'validate') {
            if (!$content || !isset($attributes['author']) || !$attributes['author']) {
                return false;
            }
            return true;
        }
        return '<a href="/library/' . urlencode($attributes['author']) . '/'
            . urlencode($content) . '">' . htmlspecialchars($content) . '</a>';
    }

    public static function processUser($action, $attributes, $content, $params, $node_object) {
        if ($action == 'validate') {
            if (!$content) {
                return false;
            }
            return true;
        }
        return '<a href="/user/' . urlencode($content) . '">' . htmlspecialchars($content) . '</a>';
    }
}
