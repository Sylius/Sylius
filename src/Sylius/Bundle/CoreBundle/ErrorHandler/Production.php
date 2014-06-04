<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\ErrorHandler;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\ExceptionHandler;

class Production extends ExceptionHandler
{
    /**
     * Registers the exception handler.
     *
     * @param Boolean $debug
     *
     * @return ExceptionHandler The registered exception handler
     */
    public static function register($debug = false)
    {

        $handler = new static($debug);

        ini_set('display_errors', 0);
        set_error_handler(array($handler, 'handleFatal'));
        register_shutdown_function(array($handler, 'handleFatal'));

        return $handler;
    }

    /**
     * Creates a new exception and passed it to our handler.  
     * This creates a uniform error screen with our exceptions.
     */
    public function handleFatal()
    {
    	$exception = new \Exception("There was an error.  Please try again later");
    	$this->handle($exception);
    }



}