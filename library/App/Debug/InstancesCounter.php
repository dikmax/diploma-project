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
 * Instances counter model
 */
class App_Debug_InstancesCounter
{
    public static function getInstancesCounts()
    {
        $classes = get_declared_classes();

        $result = array();
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->hasProperty('instancesCount')) {
                $result[$class] = $reflectionClass->getProperty('instancesCount')->getValue();
            }
        }

        return $result;
    }
}
