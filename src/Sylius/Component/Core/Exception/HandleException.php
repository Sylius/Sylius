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

namespace Sylius\Component\Core\Exception;

final class HandleException extends \RuntimeException
{
    public function __construct(string $handlerName, string $message, ?\Exception $previousException = null)
    {
        parent::__construct(
            sprintf(
                '%s was unable to handle this request. %s',
                $handlerName,
                $message
            ),
            0,
            $previousException
        );
    }
}
