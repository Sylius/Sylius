<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Exception;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class HandleException extends \RuntimeException
{
    /**
     * @param string $handlerName
     * @param string $why
     * @param \Exception|null $previousException
     */
    public function __construct($handlerName, $why,\Exception $previousException = null)
    {
        parent::__construct(
            sprintf(
                '%s was unable to handle this request, because %s',
                $handlerName,
                $why
            ),
            0,
            $previousException
        );
    }
}
