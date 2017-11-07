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

namespace Sylius\Bundle\CoreBundle;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class Controller
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function fooAction(Request $request)
    {
        $this->commandBus->handle('some command: ' . $request->getUrl(), 42);
    }
}
