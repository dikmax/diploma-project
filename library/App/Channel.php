<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Channel/Item.php';

/**
 * Channel model
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Channel
{
    /**
     * Index for database table <code>lib_channel</code>
     *
     * @var int
     */
    protected $_libChannelId;

    /**
     * Channel name
     *
     * @var string
     */
    protected $_name;

    /**
     * Channel description
     *
     * @var string
     */
    protected $_desctipion;

    /**
     * Constructs new channel object
     *
     * @param array $construct
     * Available indexes
     * <ul>
     *     <li><code>lib_channel_id</code>: database id. It must be set to null
     *         if not exists in database (<b>int</b>)</li>
     *     <li><code>id</code>: alias for <code>lib_channel_id</code> (<b>int</b>)</li>
     *     <li><code>name</code>: channel name (<b>string</b>)</li>
     *     <li><code>description</code>: channel description (<b>string</b>)</li>
     * </ul>
     */
    public function __construct(array $construct = array())
    {
        // Id
        if (isset($construct['lib_channel_id'])) {
            $this->_libChannelId = $construct['lib_channel_id'];
        } elseif (isset($construct['id'])) {
            $this->_libChannelId = $construct['id'];
        } else {
            $this->_libChannelId = null;
        }

        // Name
        if (isset($construct['name'])) {
            $this->_name = $construct['name'];
        } else {
            $this->_name = '';
        }

        // Description
        if (isset($construct['description'])) {
            $this->_desctipion = $construct['description'];
        } else {
            $this->_desctipion = '';
        }
    }

    /**
     * Get channel with specified id from db
     *
     * @param int $id
     *
     * @return App_Channel
     *
     * @throws App_Channel_Exception
     */
    public static function read($id)
    {
        if (!is_int($id)) {
            throw new App_Channel_Exception('First parameter to '
                . 'App_Channel::read() must be int');
        }

        $db = Zend_Registry::get("db");

        $row = $db->fetchRow('SELECT lib_channel_id, name, description '
             . 'FROM lib_channel '
             . 'WHERE lib_channel_id = :lib_channel_id',
            array(':lib_channel_id' => $id));

        if ($row === false) {
            require_once 'App/Channel/Exception.php';
            throw new App_Channel_Exception('Channel with id=' . $id
                . ' doesn\'t exists');
        }

        return new App_Channel($row);
    }

    /**
     * Writes channel to database
     */
    public function write() {
        $db = Zend_Registry::get("db");

        if ($this->_libChannelId === null) {
            // Creating new channel
            $data = array("name" => $this->_name,
                          "description" => $this->_desctipion);
            $db->insert('lib_channel', $data);
            $this->_libChannelId = $db->lastInsertId();
        } else {
            // Update channel
            // TODO write update channel
        }
    }

    /**
     * Return all items from channel
     *
     * @return array
     */
    public function getItems() {
        $db = Zend_Registry::get("db");

        $items = $db->fetchAll('SELECT i.lib_channel_item_id, i.item_text_id, i.item_date, '
            .     'i.author_id, i.published, t.cdate, '
            .     'lib_text_revision_id, lib_text_revision_content_id, '
            .     'r.mdate, r.revision, r.author_id as revision_author_id, '
            .     'r.changes, c.content '
            . 'FROM lib_channel_item i '
            . 'LEFT JOIN lib_text t ON i.item_text_id = t.lib_text_id '
            . 'LEFT JOIN lib_text_revision r USING (lib_text_revision_id) '
            . 'LEFT JOIN lib_text_revision_content c USING (lib_text_revision_content_id) '
            . 'WHERE i.lib_channel_id = :lib_channel_id',
            array(':lib_channel_id' => $this->_libChannelId));

        $result = array();
        foreach ($items as $i) {
            $revision = array(
                'lib_text_revision_id' => $i['lib_text_revision_id'],
                'content_id' => $i['lib_text_revision_content_id'],
                'content' => $i['content'],
                'mdate' => App_Date::fromMysqlString($i['mdate']),
                'revision' => $i['revision'],
                'changes' => $i['changes']
            );
            $text = array(
                'lib_text_id' => $i['item_text_id'],
                'revision' => $revision,
                'cdate' => App_Date::fromMysqlString($i['cdate'])
            );
            $item = array(
                'lib_channel_item_id' => $i['lib_channel_item_id'],
                'lib_channel' => $this,
                'item_text' => $text,
                'item_date' => App_Date::fromMysqlString($i['item_date']),
                'author_id' => $i['author_id'],
                'published' => $i['published']
            );
            $result[] = new App_Channel_Item($item);
        }

        return $result;
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibChannelId()
    {
        return $this->_libChannelId;
    }

    /**
     * Returns database id (alias for <code>getLibChannelId</code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libChannelId;
    }

    /**
     * Returns channel name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns channel description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_desctipion;
    }
}
