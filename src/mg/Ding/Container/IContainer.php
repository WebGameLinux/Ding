<?php
/**
 * Interface for a container.
 *
 * PHP Version 5
 *
 * @category Ding
 * @package  Container
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://www.noneyet.ar/ Apache License 2.0
 * @version  SVN: $Id$
 * @link     http://www.noneyet.ar/
 */
namespace Ding\Container;

/**
 * Interface for a container.
 *
 * PHP Version 5
 *
 * @category Ding
 * @package  Container
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://www.noneyet.ar/ Apache License 2.0
 * @link     http://www.noneyet.ar/
 */
interface IContainer
{
    /**
     * Returns a bean, by name. The bean might be a proxy, if aspected.
     * 
     * @param string $bean Bean name.
     * 
     * @result object
     */
    public function getBean($bean);

    /**
     * Register a shutdown (destroy-method) method for a bean.
     * 
     * @param object $bean   Bean to call.
     * @param string $method Method to call.
     * 
     * @see Ding\Container.IContainer::registerShutdownMethod()
     * 
     * @return void
     */
    public function registerShutdownMethod($bean, $method);
}
