<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Exception;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdateHandlingException extends \Exception
{
    /**
     * @var string
     */
    protected $flash;

    /**
     * @var int
     */
    protected $apiResponseCode;

    /**
     * @param string $message
     * @param string $flash
     * @param int $apiResponseCode
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        $message = 'Ups, something went wrong, please try again.',
        $flash = 'something_went_wrong_error',
        $apiResponseCode = 400,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->flash = $flash;
        $this->apiResponseCode = $apiResponseCode;
    }

    /**
     * @return string
     */
    public function getFlash()
    {
        return $this->flash;
    }

    /**
     * @return int
     */
    public function getApiResponseCode()
    {
        return $this->apiResponseCode;
    }
}
