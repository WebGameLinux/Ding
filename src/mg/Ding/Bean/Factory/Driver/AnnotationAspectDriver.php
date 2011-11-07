<?php
/**
 * This driver will lookup all aspect-annotated beans.
 *
 * PHP Version 5
 *
 * @category   Ding
 * @package    Bean
 * @subpackage Factory.Driver
 * @author     Marcelo Gornstein <marcelog@gmail.com>
 * @license    http://marcelog.github.com/ Apache License 2.0
 * @version    SVN: $Id$
 * @link       http://marcelog.github.com/
 *
 * Copyright 2011 Marcelo Gornstein <marcelog@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
namespace Ding\Bean\Factory\Driver;

use Ding\Bean\Lifecycle\IAfterConfigListener;
use Ding\Bean\Lifecycle\IBeforeDefinitionListener;
use Ding\Bean\BeanDefinition;
use Ding\Bean\BeanAnnotationDefinition;
use Ding\Bean\Factory\IBeanFactory;
use Ding\Container\IContainer;
use Ding\Reflection\ReflectionFactory;
use Ding\Aspect\AspectManager;
use Ding\Aspect\AspectDefinition;
use Ding\Aspect\PointcutDefinition;

/**
 * This driver will lookup all aspect-annotated beans.
 *
 * PHP Version 5
 *
 * @category   Ding
 * @package    Bean
 * @subpackage Factory.Driver
 * @author     Marcelo Gornstein <marcelog@gmail.com>
 * @license    http://marcelog.github.com/ Apache License 2.0
 * @link       http://marcelog.github.com/
 */
class AnnotationAspectDriver implements IAfterConfigListener, IBeforeDefinitionListener
{
    /**
     * Aspect manager instance.
     * @var AspectManager
     */
    private $_aspectManager = false;

    /**
     * References cache.
     * @var ICache
     */
    private $_cache;

    public function beforeDefinition(IBeanFactory $factory, $beanName, BeanDefinition $bean = null)
    {
        if (!empty($bean)) {
            return $bean;
        }
        if (isset($this->_cache[$beanName])) {
            return $this->_cache[$beanName];
        }
        return $bean;
    }

    private function _newAspect($aspectClass, $factory, $classExpression, $expression, $method, $type)
    {
        // Create bean.
        $aspectBeanName = 'AnnotationAspectedBean' . rand(1, microtime(true));
        $aspectBean = new BeanDefinition($aspectBeanName);
        $aspectBean->setScope(BeanDefinition::BEAN_SINGLETON);
        $aspectBean->setClass($aspectClass);
        $this->_cache[$aspectBeanName] = $aspectBean;
        $pointcutName = 'PointcutAnnotationAspectDriver' . rand(1, microtime(true));
        $pointcutDef = new PointcutDefinition($pointcutName, $expression, $method);
        $aspectName = 'AnnotationAspected' . rand(1, microtime(true));
        $aspectDef = new AspectDefinition(
            $aspectName, array($pointcutName), $type,
            $aspectBeanName, $classExpression
        );
        $this->_aspectManager->setPointcut($pointcutDef);
        $this->_aspectManager->setAspect($aspectDef);
    }

    /**
     * (non-PHPdoc)
     * @see Ding\Bean\Lifecycle.IAfterConfigListener::afterConfig()
     */
    public function afterConfig(IContainer $factory)
    {
        // Create aspects and pointcuts.
        $aspects = ReflectionFactory::getClassesByAnnotation('Aspect');
        foreach ($aspects as $name) {
            $annotations = ReflectionFactory::getClassAnnotations($name);
            foreach ($annotations as $key => $annotation) {
                if ($key == 'class') {
                    continue;
                }
                if (isset($annotation['MethodInterceptor'])) {
                    $arguments = $annotation['MethodInterceptor']->getArguments();
                    $classExpression = $arguments['class-expression'];
                    $expression = $arguments['expression'];
                    $method = $key;
                    $this->_newAspect(
                        $name, $factory, $classExpression, $expression, $method, AspectDefinition::ASPECT_METHOD
                    );
                }
                if (isset($annotation['ExceptionInterceptor'])) {
                    $arguments = $annotation['ExceptionInterceptor']->getArguments();
                    $classExpression = $arguments['class-expression'];
                    $expression = $arguments['expression'];
                    $method = $key;
                    $this->_newAspect(
                        $name, $factory, $classExpression, $expression, $method, AspectDefinition::ASPECT_EXCEPTION
                    );
                }
            }
        }
    }

    /**
     * Constructor.
     *
     * @param \Ding\Aspect\AspectManager $aspectManager
     *
     * @return void
     */
    public function __construct(\Ding\Aspect\AspectManager $aspectManager)
    {
        $this->_aspectManager = $aspectManager;
    }
}