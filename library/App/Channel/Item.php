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
 * Channel item model
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Channel_Item
{
    /**
     * Index for database table <code>lib_channel_item</code>
     *
     * @var int
     */
    protected $_libChannelItemId;
    
    /**
     * Channel to which item belongs
     *
     * @var App_Channel
     */
    protected $_libChannel;
    
    /**
     * Item text
     *
     * @var App_Text
     */
    protected $_itemText;
    
    /**
     * Item datetime
     *
     * @var App_Date
     */
    protected $_itemDate;
    
    // TODO add author field here
    
    /**
     * <code>true</code> if item is published
     *
     * @var boolean
     */
    protected $_published;
    
    /**
     * Constructs new App_Channel_Item object
     *
     * @param array $construct
     * Available indexes:
     * <ul>
     *   <li><code>lib_channel_item_id</code>: database id.
     *       It must be set to null if not exists in database (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_channel_item_id</code> (<b>int</b>)</li>
     *   <li><code>lib_channel</code>: Channel to which item belongs
     *       (<b>App_Channel</b>, <i>required</i>)</li>
     *   <li><code>item_text</code>: Item text (<b>App_Text|array</b>)</li>
     *   <li><code>item_date</code>: Item date (<b>int|string|array|App_Date</b>)</li>
     *   <li><code>published</code>: Published property (<b>boolean</b>)</li>
     * </ul>
     *
     * @throws App_Channel_Item_Exception
     */
    public function __construct(array $construct = array())
    {
        // Id
        if (isset($construct['lib_channel_item_id'])) {
            $this->_libChannelItemId = $construct['lib_channel_item_id'];
        } elseif (isset($construct['id'])) {
            $this->_libChannelItemId = $construct['id'];
        } else {
            $this->_libChannelItemId = null;
        }
        
        // libChannel
        if (!isset($construct['lib_channel'])) {
            throw new App_Channel_Item_Exception("'lib_channel' index is required.");
        }
        if ($construct['lib_channel'] instanceof App_Channel) {
            $this->_libChannel = $construct['lib_channel'];
        } else {
            throw new App_Channel_Item_Exception("'lib_channel' must be instance of App_Channel");
        }
        
        // itemText
        if (isset($construct['item_text'])) {
            if ($construct['item_text'] instanceof App_Text) {
                $this->_itemText = $construct['item_text'];
            } elseif (is_array($construct['item_text'])) {
                $this->_itemText = new App_Text($construct['item_text']);
            } else {
                throw new App_Channel_Item_Exception("'item_text' must be '
                    . 'instance of App_Text or array or null");
            }
        } else {
            $this->_itemText = new App_Text(array("text" => ""));
        }
        
        // itemDate
        if (isset($construct['item_date'])) {
            $this->_itemDate = new App_Date($construct['item_date']);
        } else {
            $this->_itemDate = App_Date::now();
        }
        
        // TODO author must be here.
        
        // published
        if (isset($construct['published'])) {
            $this->_published = $construct['published'];
        } else {
            $this->_published = false;
        }
    }
    
    public function write()
    {
        $db = Zend_Registry::get("db");
        
        if ($this->_libChannelItemId === null) {
            // Create new
            
            if ($this->_libChannel === null) {
                throw new App_Channel_Item_Exception("'\$_libChannel' isn't defined.");
            }
            $data = array("lib_channel_id" => $this->_libChannel->getId(),
                          "item_text_id" => $this->_itemText->getId(),
                          "item_date" => $this->_itemDate->toMysqlString(),
                          "author_id" => 1, // TODO insert real user id
                          "published" => $this->_published);
            $db->insert('lib_channel_item', $data);
            $this->_libChannelItemId = $db->lastInsertId();
        } else {
            // Update
            // TODO write update
        }
    }
    
    /*
     * Setters and getters
     */
    
    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibChannelItemId()
    {
        return $this->_libChannelItemId;
    }
    
    /**
     * Returns database id (alias for <code>getLibChannelItemId</code>)
     *
     * @return unknown
     */
    public function getId()
    {
        return $this->_libChannelItemId;
    }
    
    /**
     * Returns channel to which item belongs
     *
     * @return App_Channel
     */
    public function getLibChannel()
    {
        return $this->_libChannel;
    }
    
    /**
     * Returns item text
     *
     * @return App_Text
     */
    public function getItemText()
    {
        return $this->_itemText;
    }
    
    /**
     * Returns item date
     *
     * @return App_Date
     */
    public function getItemDate()
    {
        return $this->_itemDate;
    }
    
    /**
     * Returns item published state
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->_published;
    }
}