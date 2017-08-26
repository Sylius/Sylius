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
 * This exception is thrown when something goes wrong during the deletion of a
 * resource.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class DeleteHandlingException extends UpdateHandlingException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        string $message = 'Ups, something went wrong, please try again.',
        string $flash = 'something_went_wrong_error',
        int $apiResponseCode = 500,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $flash, $apiResponseCode, $code, $previous);
    }
}
