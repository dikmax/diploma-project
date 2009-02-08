<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/Label.php';

/**
 * Custom label decorator with ability to apply custom class to block tag
 */
class App_Form_Decorator_Label extends Zend_Form_Decorator_Label
{
    /**
     * Render a label
     * @see Zend_Form_Decorator_Label::render()
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $class     = $this->getClass();
        $options   = $this->getOptions();


        if (empty($label) && empty($tag)) {
            return $content;
        }

        if (!empty($label)) {
            $labelOptions = $options;
            $labelOptions['class'] = $class;
            if (isset($labelOptions['tagClass'])) {
                unset($labelOptions['tagClass']);
            }
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $labelOptions);
        } else {
            $label = '&nbsp;';
        }

        if (null !== $tag) {
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decoratorOptions = array('tag' => $tag);
            if (isset($options['tagClass'])) {
                $decoratorOptions['class'] = $options['tagClass'];
            }
            $decorator->setOptions($decoratorOptions);
            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }

        return '';
    }
}
