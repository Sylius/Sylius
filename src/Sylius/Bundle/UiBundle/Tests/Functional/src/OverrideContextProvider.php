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

namespace Sylius\Bundle\UiBundle\Tests\Functional\src;

use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;

final class OverrideContextProvider implements ContextProviderInterface
{
    public function provide(array $templateContext, array $blockContext): array
    {
        return $blockContext + ['templateContext' => $templateContext];
    }
}
