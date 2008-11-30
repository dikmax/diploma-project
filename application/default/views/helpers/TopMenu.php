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
 * Helper for generating top menu
 */
class App_View_Helper_TopMenu extends Zend_View_Helper_Abstract
{
    /**
     * Array of items
     *
     * @var array
     */
    protected $_items;

    /**
     * Name of selected item
     *
     * @var string
     */
    protected $_selectedName;

    /**
     * Id of selected item
     *
     * @var int
     */
    protected $_selectedId;

    /**
     * Constructs helper
     */
    public function __construct()
    {
        $this->_items = array();

        $this->_selectedName = null;
        $this->_selectedId = null;
    }

    /**
     * Renders top menu
     */
    public function topMenu()
    {
        $result = '<ul class="top-menu">';

        foreach ($this->_items as $item) {
            if ($item['selected']) {
                $result .= '<li class="selected">'
                        . $item['name']
                        . '</li>';
            } else {
                $result .= '<li>'
                        . '<a href="' . $item['link'] . '">'
                        . $item['name']
                        . '</a>'
                        . '</li>';
            }
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * Adds menu item
     *
     * @param string $name Menu item name
     * @param string $link Menu item link
     * @param boolean $selected Is menu item selected
     *
     * @return App_View_Helper_TopMenu
     */
    public function addItem($name, $link, $selected = false) {
        $this->_items[] = array(
            'name' => $name,
            'link' => $link,
            'selected' => $selected
        );

        if ($selected) {
            if ($this->_selectedId !== null) {
                $this->_items[$this->_selectedId]['selected'] = false;
            }

            $this->_selectedName = $name;
            $this->_selectedId = count($this->_items) - 1;
        }

        return $this;
    }

    public function selectItem($name) {
        if ($this->_selectedName === $name) {
            return;
        }

        if ($this->_selectedId !== null) {
            $this->_items[$this->_selectedId]['selected'] = false;
        }

        foreach ($this->_items as $key => $item) {
            if ($item['name'] === $name) {
                $this->_selectedId = $key;
                $this->_selectedName = $name;
                $this->_items[$key]['selected'] = true;
            }
        }
    }
}