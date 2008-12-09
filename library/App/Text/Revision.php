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
 * Text revision model class. Internal use only
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Text_Revision {
    /**
     * Index for database table <code>lib_text_revision</code>
     *
     * @var int
     */
    protected $_libTextRevisionId;
    
    /**
     * Connected text
     *
     * @var App_Text
     */
    protected $_libText;

    /**
     * Index for database table <code>lib_text_revision_content</code>
     *
     * @var int
     */
    protected $_contentId;
    
    /**
     * Revision content
     *
     * @var string
     */
    protected $_content;
    
    /**
     * Date of modification
     *
     * @var App_Date
     */
    protected $_mdate;
    
    /**
     * Revision number
     *
     * @var int
     */
    protected $_revision;

    /**
     * Changes in this revision
     *
     * @var string
     */
    protected $_changes;
    
    /**
     * Constructs App_Text_Revision object
     *
     * @param array $construct
     * Available indexes:
     * <ul>
     *   <li><code>lib_text_revision_id</code>: database id.
     *       It must be set to null if not exists in database (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_text_revision_id</code> (<b>int</b>)</li>
     *   <li><code>lib_text</code>: connected text (<b>App_Text</b>)</li>
     *   <li><code>content_id</code>: database id.
     *       It must be set to null if not exists in database (<b>int</b>)</li>
     *   <li><code>content</code>: revision text (<b>string</b>)</li>
     *   <li><code>text</code>: alias for <code>content</code> (<b>string</b>)</li>
     *   <li><code>mdate</code>: timestamp, array or Zend_Date of text modify date.
     *       If not set it will be equal to current date
     *       (<b>int|string|array|App_Date</b>)</li>
     *   <li><code>revision</code>: revision number (<b>int</b>)</li>
     *   <li><code>changes</code>: changes made in this revision (<b>string</b>)</li>
     * </ul>
     *
     * @throws App_Text_Revision_Exception
     */
    public function __construct(array $construct = array())
    {
        // lib_text_revision_id
        if (isset($construct['lib_text_revision_id'])) {
            $this->_libTextRevisionId = $construct['lib_text_revision_id'];
        } elseif (isset($construct['id'])) {
            $this->_libTextRevisionId = $construct['id'];
        } else {
            $this->_libTextRevisionId = null;
        }
        
        // lib_text_id
        if (isset($construct['lib_text'])) {
            if (!($construct['lib_text'] instanceof App_Text)) {
                throw new App_Text_Revision_Exception("'lib_text' index must be instance of App_Text or null");
            }
            $this->_libText = $construct['lib_text'];
        } else {
            $this->_libText = null;
        }
        
        // Dependent table lib_text_revision_content
        if (isset($construct['content_id'])) {
            if (!is_numeric($construct['content_id'])) {
                throw new App_Text_Revision_Exception("'content_id' index must be int or null");
            }
            $this->_contentId = $construct['content_id'];
        }
        if (isset($construct['content']) && is_string($construct['content'])) {
            $this->_content = $construct['content'];
        } elseif (isset($construct['text']) && is_string($construct['text'])) {
            $this->_content = $construct['text'];
        } else {
            $this->_content = '';
        }

        // mdate
        $this->_mdate = isset($construct['mdate'])
            ? new App_Date($construct['mdate'])
            : $this->_mdate = App_Date::now();
        
        // revision
        if (!isset($construct['revision'])) {
            $this->_revision = 1;
        } else {
            if (!is_numeric($construct['revision'])) {
                throw new App_Text_Revision_Exception("'revision' index must be integer");
            }
            $this->_revision = $construct['revision'];
        }
        
        // TODO revision author ('author_id' index)
        
        // changes
        if (isset($construct['changes'])) {
            if (!is_string($construct['changes'])) {
                throw new App_Text_Revision_Exception("'changes' index must be string or null");
            }
            $this->_changes = $construct['changes'];
        } else {
            $this->_changes = null;
        }
    }

    /**
     * Writes/updates revision into database
     * 
     * @throws App_Text_Revision_Exception
     */
    public function write()
    {
        $db = Zend_Registry::get('db');
        
        if ($this->_libTextRevisionId === null) {
            // Creating revision
            
            $user = App_User_Factory::getSessionUser();
            if (!$user) {
                throw new App_Text_Revision_Exception('Guest user can\'t edit texts');
            }
            $table = new App_Db_Table_TextRevision();
            
            // Creating 'lib_text_revision_content' record
            $this->_contentId = $table->insert(array("content" => $this->_content));

            if ($this->_changes === null) {
                $this->_changes = "First revision";
            }
            
            // Creating 'lib_text_revision' record
            if ($this->_revision !== null) {
                $revision = $this->_revision;
            } else if ($this->_libText !== null && $this->_libText->getId() !== null) {
                $revision = $db->fetchOne('(SELECT max(revision) + 1 '
                    . 'FROM lib_text_revision '
                    . 'WHERE lib_text_id = :lib_text_id)', 
                    array(
                        ':lib_text_id' => $this->_libText->getId()
                    ));
                //$revision = new Zend_Db_Expr($db->quoteInto());
            } else {
                $revision = 1;
            }
            $data = array('lib_text_revision_content_id' => $this->_contentId,
                          'mdate' => $this->_mdate->toMysqlString(),
                          'revision' => $revision,
                          'author_id' => $user->getId(),
                          'changes' => $this->_changes);
            if ($this->_libText !== null && $this->_libText->getLibTextId() !== null) {
                $data['lib_text_id'] = $this->_libText->getId();
            }
            $db->insert('lib_text_revision', $data);
            $this->_libTextRevisionId = $db->lastInsertId();
        } else {
            throw new App_Text_Revision_Exception('Revision could not be updated');
        }
    }

    /**
     * Updates lib_text_id
     *
     * @throws App_Text_Exception
     * @throws App_Text_Revision_Exception
     */
    public function writeLibTextId()
    {
        $db = Zend_Registry::get('db');

        if (!isset($this->_libText)) {
            throw new App_Text_Revision_Exception('$_libText isn\'t set');
        }
        if ($this->_libText->getId() === null) {
            throw new App_Text_Exception("lib_text_id isn't set");
        }
        $data = array('lib_text_id' => $this->_libText->getId());
        $db->update('lib_text_revision', $data,
            $db->quoteInto("lib_text_revision_id = ?", $this->_libTextRevisionId));
    }

    /**
     * Is text have been changed
     *
     * @return boolean
     */
    public function isChanged()
    {
        // TODO Write isChanged function
        return false;
    }
    
    /*
     * Setters and getters
     */
    
    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibTextRevisionId()
    {
        return $this->_libTextRevisionId;
    }
    
    /**
     * Returns database id (alias for <code>getLibTextRevisionId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libTextRevisionId;
    }

    /**
     * Returns linked <code>App_Text</code> object
     *
     * @return App_Text
     */
    public function getLibText()
    {
        return $this->_libText;
    }
    
    /**
     * Returns database id for content
     *
     * @return int
     */
    public function getContentId()
    {
        return $this->_contentId;
    }
    
    /**
     * Returns revision content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * Returns revision content (alias for <code>getContent</code>)
     *
     * @return string
     */
    public function getText()
    {
        return $this->_content;
    }
    
    /**
     * Returns revision date
     *
     * @return App_Date
     */
    public function getMdate()
    {
        return $this->_mdate;
    }
    
    /**
     * Returns revision number
     *
     * @return int
     */
    public function getRevision()
    {
        return $this->_revision;
    }
    
    /**
     * Return changes in this revision
     *
     * @return string
     */
    public function getChanges()
    {
        return $this->_changes;
    }

    /**
     * Sets new revision content (creates new revision)
     * 
     * @param string $content
     * @return boolean Success
     */
    public function setContent($content)
    {
        if ($this->_content == $content) {
            // Content is same. Skip
            return false;
        }
        // Updating fields for new revision
        $this->_libTextRevisionId = null;
        $this->_contentId = null;
        $this->_content = $content;
        $this->_mdate = App_Date::now();
        $this->_revision = null;
        $this->_changes = "Update text";
        
        return true;
    }
    
    /**
     * Set new revision content, creates new revision (alias for <code>setContent</code>)
     * 
     * @param string $text
     * @return boolean Success
     */
    public function setText($text)
    {
        return $this->setContent($text);
    }
}
?>