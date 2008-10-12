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
 * Text model class
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Text extends App_Acl_Resource_Abstract {
    /**
     * Index for database table <code>lib_text</code>
     *
     * @var int
     */
    protected $_libTextId;

    /**
     * Text revision
     *
     * @var App_Text_Revision
     */
    protected $_revision;

    /**
     * Date of creation
     *
     * @var App_Date
     */
    protected $_cdate;

    /**
     * Constructs new App_Text object
     *
     * @param array $construct
     * Available indexes:
     * <ul>
     *   <li><code>lib_text_id</code>: database id. It must be set to null if
     *       not exists in database (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_text_id</code> (<b>int</b>)</li>
     *   <li><code>revision</code>: <code>App_Text_Revision</code> object or array
     *       to pass to <code>App_Text_Revision</code> constructor. Required, if
     *       <code>lib_text_id</code> or <code>id</code> is set.
     *       (<b>array|App_Text_Revision</b>)</li>
     *   <li><code>text</code>: Text. Usable if <code>revision</code> isn't defined
     *       (<b>string</b>)</li>
     *   <li><code>cdate</code>: timestamp, array or App_Date of text create date.
     *       If not set it will be equal to current date (<b>int|array|App_Date</b>)</li>
     *   <li><code>mdate</code>: timestamp, array or Zend_Date of text modify date.
     *       If not set it will be equal to current date. Usable if <code>revision</code>
     *       isn't defined (<b>int|string|array|App_Date</b>)</li>
     * </ul>
     *
     * @throws App_Text_Exception
     */
    public function __construct(array $construct = array())
    {
        // Id
        if (isset($construct['lib_text_id'])) {
            $this->_libTextId = $construct['lib_text_id'];
        } elseif (isset($construct['id'])) {
            $this->_libTextId = $construct['id'];
        } else {
            $this->_libTextId = null;
        }

        // Revision
        if ($this->_libTextId !== null) {
            if (!isset($construct['revision'])) {
                throw new App_Text_Exception("'revision' index must be set");
            }
            if ($construct['revision'] instanceof App_Text_Revision) {
                // TODO check, if revision is belongs to this text
                $this->_revision = $construct['revision'];
            } elseif (is_array($construct['revision'])) {
                $revision_params = $construct['revision'];
                $revision_params['lib_text'] = $this;
                $this->_revision = new App_Text_Revision($revision_params);
            } else {
                throw new App_Text_Exception("'revision' index must be App_Text_Revision or array");
            }
        } else {
            $revision_params = array();
            if (isset($construct['text']) && is_string($construct['text'])) {
                $revision_params['content'] = $construct['text'];
            }
            if (isset($construct['mdate'])) {
                $revision_params['mdate'] = $construct['mdate'];
            }
            $revision_params['lib_text'] = $this;
            $this->_revision = new App_Text_Revision($revision_params);
        }

        $this->_cdate = isset($construct['cdate'])
            ? new App_Date($construct['cdate'])
            : App_Date::now();

        $this->_text = null;

        $this->registerResource();
    }

    /**
     * Writes/updates text into database
     */
    public function write()
    {
        $db = Zend_Registry::get('db');

        if ($this->_libTextId === null) {
            //Creates new text
            $db->beginTransaction();

            try {
                $this->_revision->write();

                $data = array('lib_text_revision_id' => $this->_revision->getId(),
                              'cdate' => $this->_cdate->toMysqlString());
                $db->insert('lib_text', $data);
                $this->_libTextId = $db->lastInsertId();

                $this->_revision->writeLibTextId();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            // Updates existing text

            $db->beginTransaction();

            try {
                $this->_revision->write();

                $data = array('lib_text_revision_id' => $this->_revision->getId());
                $db->update('lib_text', $data,
                    $db->quoteInto('lib_text_id = ?', $this->_libTextId));

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Gets text with specified id from database
     *
     * @param int $id
     * @return App_Text
     * @throws App_Text_Excaption
     */
    public static function read($id)
    {
        if (!is_numeric($id)) {
            throw new App_Text_Exception('First parameter to App_Text::read() must be int');
        }

        $db = Zend_Registry::get('db');

        $result = $db->fetchRow('SELECT t.lib_text_id, t.cdate, '
            .     'lib_text_revision_id, lib_text_revision_content_id, '
            .     'r.mdate, r.revision, r.author_id, r.changes, c.content '
            . 'FROM lib_text t '
            . 'LEFT JOIN lib_text_revision r USING (lib_text_revision_id) '
            . 'LEFT JOIN lib_text_revision_content c USING (lib_text_revision_content_id) '
            . 'WHERE t.lib_text_id = :lib_text_id',
            array(':lib_text_id' => $id)
        );

        if ($result === false) {
            throw new App_Text_Exception("Text with id=$id doesn't exists");
        }

        //Transforming $result to pass to constructor
        // TODO Add author support here
        $revision = array(
            'lib_text_revision_id' => $result['lib_text_revision_id'],
            'content_id' => $result['lib_text_revision_content_id'],
            'content' => $result['content'],
            'mdate' => App_Date::fromMysqlString($result['mdate']),
            'revision' => $result['revision'],
            'changes' => $result['changes']
        );
        $text = array(
            'lib_text_id' => $result['lib_text_id'],
            'revision' => $revision,
            'cdate' => App_Date::fromMysqlString($result['cdate'])
        );

        return new App_Text($text);
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibTextId()
    {
        return $this->_libTextId;
    }

    /**
     * Returns database id (alias for <code>getLibTextId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libTextId;
    }

    /**
     * Returns <code>App_Text_Revision</code> object
     *
     * @return App_Text_Revision
     */
    public function getRevision()
    {
        return $this->_revision;
    }

    /**
     * Returns latest text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_revision->getText();
    }

    /**
     * Returns create date
     *
     * @return App_Date
     */
    public function getCdate()
    {
        return $this->_cdate;
    }

    /**
     * Sets new text
     *
     * @param string $text newText
     * @param boolean $noWrite <code>true</code> if don't write to database
     */
    public function setText($text, $noWrite = false)
    {
        $this->_revision->setText($text);
        if (!$noWrite) {
            $this->write();
        }
    }

    /*
     * App_Acl_Resource_Abstract
     */

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        if ($this->_libTextId !== null) {
            return "wiki-" . $this->_libTextId;
        }
        return "wiki-new";
    }

    /**
     * Returns resource parent (for registering)
     *
     * @return string
     */
    protected function getResourceParentId()
    {
        return 'wiki';
    }
}
