<?php
/**
 * This is a bean that will call your own error handler (only if it implements
 * IErrorHandler).
 *
 * PHP Version 5
 *
 * @category   Ding
 * @package    Helpers
 * @subpackage ErrorHandler
 * @author     Marcelo Gornstein <marcelog@gmail.com>
 * @license    http://www.noneyet.ar/ Apache License 2.0
 * @version    SVN: $Id$
 * @link       http://www.noneyet.ar/
 */
namespace Ding\Helpers\ErrorHandler;

/**
 * This is a bean that will call your own error handler (only if it implements
 * IErrorHandler).
 *
 * PHP Version 5
 *
 * @category   Ding
 * @package    Helpers
 * @subpackage ErrorHandler
 * @author     Marcelo Gornstein <marcelog@gmail.com>
 * @license    http://www.noneyet.ar/ Apache License 2.0
 * @link       http://www.noneyet.ar/
 */
class ErrorHandlerHelper
{
    /**
     * log4php logger or our own.
     * @var Logger
     */
    private $_logger;

    /**
     * Error handler to call.
     * @var IErrorHandler
     */
    private $_handler;

    /**
     * Set a handler to call upon errors.
     *
     * @param IErrorHandler $handler Handler to call.
     *
     * @return void
     */
    public function setErrorHandler(IErrorHandler $handler)
    {
        $this->_handler = $handler;
    }

    /**
     * This was set by set_error_handler.
     *
     * @param integer $errno   PHP Error type.
     * @param string  $errstr  Error message.
     * @param string  $errfile File that triggered the error.
     * @param integer $errline Line that triggered the error.
     */
    public function handle($errno, $errstr, $errfile, $errline)
    {
        $info = new ErrorInfo($errno, $errstr, $errfile, $errline);
        if ($this->_logger->isDebugEnabled()) {
            $this->_logger->debug(
                implode(' | ', array($errno, $errstr, $errfile, $errline))
            );
        }
        $this->_handler->handle($info);
        return true;
    }

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_logger = \Logger::getLogger('Ding.ErrorHandlerHelper');
    }
}