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

namespace Sylius\Bundle\ApiBundle\Provider;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/** @experimental */
class LiipProductImageFilterProvider implements ProductImageFilterProviderInterface
{
    use ContainerAwareTrait;

    public function provide(): array
    {
        return $this->container->getParameter('liip_imagine.filter_sets');
    }
}
