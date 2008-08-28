<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

class TestController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Fills Database with test data
        $text = new App_Text(array("text" => "Тест"));
        $text->write();
        
        $channel = new App_Channel(array("name" => "Имя",
                                         "description" => "Описание"));
        $channel->write();
        
        $item = new App_Channel_Item(array("lib_channel" => $channel,
                                           "item_text" => $text,
                                           "published" => true));
        $item->write();
        
        echo "Типа фсе";
        $this->_helper->viewRenderer->setNoRender(true);
    }
}