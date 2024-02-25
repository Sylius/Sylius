<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Command;

trigger_deprecation(
    'sylius/ui-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
    DebugTemplateEventCommand::class,
    \Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand::class,
);

class_exists(\Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand::class);

if (false) {
    final class DebugTemplateEventCommand
    {
    }
}
