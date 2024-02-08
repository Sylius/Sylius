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

namespace Sylius\Bundle\UiBundle\Tests\Functional\src;

use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class CustomContextProvider implements ContextProviderInterface
{
    public function provide(array $templateContext, TemplateBlock $templateBlock): array
    {
        return $templateContext + ['custom' => 'yolo'];
    }

    public function supports(TemplateBlock $templateBlock): bool
    {
        return 'custom_context_provider' === $templateBlock->getEventName();
    }
}
