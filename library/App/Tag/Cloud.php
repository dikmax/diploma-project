<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Tag/Cloud/Reader/Interface.php';
require_once 'App/Tag/Cloud/Writer/Interface.php';

/**
 * App_Tag_Cloud description
 */
class App_Tag_Cloud
{
    /**
     * Tag cloud reader
     *
     * @var App_Tag_Cloud_Reader_Interface
     */
    protected $_reader;

    /**
     * Tag cloud writer
     *
     * @var App_Tag_Cloud_Writer_Interface
     */
    protected $_writer;

    /**
     * Min result size
     *
     * @var int
     */
    protected $_minSize;

    /**
     * Max result size
     *
     * @var int
     */
    protected $_maxSize;

    /**
     * Constructs tag cloud object
     *
     * @param array $construct
     * Available indices
     * <ul>
     *   <li><code>reader</code>: cloud reader (<b>App_Tag_Cloud_Reader_Interface</b>)</li>
     *   <li><code>writer</code>: cloud writer (<b>App_Tag_Cloud_Writer_Interface</b>)</li>
     *   <li><code>min_size</code>: min result size (<b>int</b>)</li>
     *   <li><code>max_size</code>: max result size (<b>int</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        if (isset($construct['reader'])) {
            $this->setReader($construct['reader']);
        } else {
            $this->_reader = null;
        }

        if (isset($construct['writer'])) {
            $this->setWriter($construct['writer']);
        } else {
            $this->_writer = null;
        }

        $this->_minSize = isset($construct['min_size']) ? $construct['min_size'] : 0;

        $this->_maxSize = isset($construct['max_size']) ? $construct['max_size'] : 100;
    }

    /**
     * Process tag cloud (read data from reader and send it to writer)
     *
     * @throws App_Tag_Cloud_Exception when reader or writer isn't defined
     */
    public function process()
    {
        if ($this->_reader === null) {
            throw new App_Tag_Cloud_Exception('Tag cloud reader isn\'t defined');
        }
        if ($this->_writer === null) {
            throw new App_Tag_Cloud_Exception(('Tag cloud writer isn\'t defined'));
        }

        $tags = $this->_reader->readTagCloudList();

        $this->_writer->writeTagCloudStart();

        list($min, $max) = $tags->getMinMax();

        $spread = $max-$min;
        if ($spread < 0) {
            $this->_writer->writeTagCloudEmpty();
        } else {
            if ($spread == 0) {
                $spread = 1;
            }

            $step = ($this->_maxSize - $this->_minSize) / $spread;

            foreach ($tags as $tag) {
                $this->_writer->writeTagCloudItem($tag->name,
                    round($this->_minSize + ($tag->count - $min) * $step),
                    $tag->count);
            }
        }

        $this->_writer->writeTagCloudEnd();
    }

    /**
     * Returns tag cloud reader
     *
     * @return App_Tag_Cloud_Reader_Interface
     */
    public function getReader()
    {
        return $this->_reader;
    }

    /**
     * Sets tag cloud reader
     *
     * @param App_Tag_Cloud_Reader_Interface $reader
     *
     * @return App_Tag_Cloud
     */
    public function setReader(App_Tag_Cloud_Reader_Interface $reader)
    {
        $this->_reader = $reader;

        return $this;
    }

    /**
     * Returns tag cloud writer
     *
     * @return App_Tag_Cloud_Writer_Interface
     */
    public function getWriter()
    {
        return $this->_writer;
    }

    /**
     * Sets tag cloud writer
     *
     * @param App_Tag_Cloud_Writer_Interface $writer
     *
     * @return App_Tag_Cloud
     */
    public function setWriter(App_Tag_Cloud_Writer_Interface $writer)
    {
        $this->_writer = $writer;

        return $this;
    }

    /**
     * Returns maximum result size
     *
     * @return int
     */
    public function getMaxSize()
    {
        return $this->_maxSize;
    }

    /**
     * Sets minimum result size
     *
     * @param int $maxSize
     *
     * @return App_Tag_Cloud
     */
    public function setMaxSize($maxSize)
    {
        $this->_maxSize = $maxSize;

        return $this;
    }

    /**
     * Returns minimum result size
     *
     * @return int
     */
    public function getMinSize()
    {
        return $this->_minSize;
    }

    /**
     * Sets minimum result size
     *
     * @param int $minSize
     *
     * @return App_Tag_Cloud
     */
    public function setMinSize($minSize)
    {
        $this->_minSize = $minSize;

        return $this;
    }

}