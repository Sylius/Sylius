<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface RequestBasedHandlerInterface
{
    /**
     * @param Request $request
     *
     * @throws HandleException
     */
    public function handle(Request $request);
}
