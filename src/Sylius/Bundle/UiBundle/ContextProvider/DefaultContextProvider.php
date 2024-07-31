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

namespace Sylius\Bundle\UiBundle\ContextProvider;

use Sylius\Bundle\UiBundle\Registry\Block;

final class DefaultContextProvider implements ContextProviderInterface
{
    public function provide(array $templateContext, Block $templateBlock): array
    {
        return array_replace($templateBlock->getContext(), $templateContext);
    }

    public function supports(Block $templateBlock): bool
    {
        return true;
    }
}
