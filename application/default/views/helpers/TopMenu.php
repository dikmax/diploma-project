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
     * Array of items (left part)
     *
     * @var array
     */
    protected $_itemsLeft;

    /**
     * Array of items (right part)
     *
     * @var array
     */
    protected $_itemsRight;

    /**
     * Id of selected item (left part)
     *
     * @var string
     */
    protected $_selectedIdLeft;

    /**
     * Id of selected item (right part)
     *
     * @var string
     */
    protected $_selectedIdRight;

    /**
     * Constructs helper
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->_itemsLeft = array();
        $this->_itemsRight = array();

        $this->_selectedIdLeft = null;
        $this->_selectedIdRight = null;
    }

    public function clear()
    {
        $this->init();
    }

    /**
     * Renders top menu
     *
     * @return string
     */
    public function topMenu()
    {
        $result = '';
        $mainSelectedWithLink = false;

        // Right submenu
        if (count($this->_itemsRight) > 0) {
            $result .= '<ul class="top-menu-right">';
            foreach ($this->_itemsRight as $item) {
                if ($item['selected']) {
                    $mainSelectedWithLink = true;
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
        }

        // Left menu
        $result .= '<ul class="top-menu-left">';

        foreach ($this->_itemsLeft as $item) {
            if ($item['selected']) {
                $result .= '<li class="selected">';
                if ($mainSelectedWithLink) {
                    $result .= '<a href="' . $item['link'] . '">'
                            . $item['name']
                            . '</a>';
                } else {
                    $result .= $item['name'];
                }
                $result .= '</li>';
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
     * @param string $index menu item index
     * @param string $name Menu item name
     * @param string $link Menu item link
     * @param boolean $isRight Menu item position
     * @param boolean $selected Is menu item selected
     *
     * @return App_View_Helper_TopMenu
     */
    public function addItem($index, $name, $link, $isRight = false, $selected = false) {
        if ($isRight) {
            $this->_itemsRight[$index] = array(
                'name' => $name,
                'link' => $link,
                'selected' => $selected
            );

            if ($selected) {
                if ($this->_selectedIdRight !== null) {
                    $this->_itemsRight[$this->_selectedIdRight]['selected'] = false;
                }

                $this->_selectedIdRight = $index;
            }
        } else {
            $this->_itemsLeft[$index] = array(
                'name' => $name,
                'link' => $link,
                'selected' => $selected
            );

            if ($selected) {
                if ($this->_selectedIdLeft !== null) {
                    $this->_itemsLeft[$this->_selectedIdLeft]['selected'] = false;
                }

                $this->_selectedIdLeft = $index;
            }
        }


        return $this;
    }

    /**
     * Selects menu items
     *
     * @param string $index menu item index
     * @param boolean $isRight use right menu
     */
    public function selectItem($index, $isRight = false) {
        if ($isRight) {
            if ($this->_selectedIdRight === $index) {
                return;
            }

            if ($this->_selectedIdRight !== null) {
                $this->_itemsRight[$this->_selectedIdRight]['selected'] = false;
            }

            $this->_itemsRight[$index]['selected'] = true;
            $this->_selectedIdRight = $index;
        } else {
            if ($this->_selectedIdLeft === $index) {
                return;
            }

            if ($this->_selectedIdLeft !== null) {
                $this->_itemsLeft[$this->_selectedIdLeft]['selected'] = false;
            }

            $this->_itemsLeft[$index]['selected'] = true;
            $this->_selectedIdLeft = $index;
        }
    }
}