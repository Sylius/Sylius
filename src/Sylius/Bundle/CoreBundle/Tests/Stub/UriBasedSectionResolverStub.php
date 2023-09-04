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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsUriBasedSectionResolver;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;

#[AsUriBasedSectionResolver(priority: 20)]
final class UriBasedSectionResolverStub implements UriBasedSectionResolverInterface
{
    public function getSection(string $uri): SectionInterface
    {
        return new ShopSection();
    }
}
