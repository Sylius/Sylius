<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Exception;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ResourceException extends \Exception
{
    /**
     * @var string
     */
    protected $flash;

    /**
     * @param string $message
     * @param string $flash
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        $message = 'Ups, something went wrong, please try again.',
        $flash = 'something_went_wrong_error',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->flash = $flash;
    }

    /**
     * @return string
     */
    public function getFlash()
    {
        return $this->flash;
    }
}
