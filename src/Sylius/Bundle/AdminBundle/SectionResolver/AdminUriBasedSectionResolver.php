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

namespace Sylius\Bundle\AdminBundle\SectionResolver;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminUriBasedSectionResolver implements UriBasedSectionResolverInterface
{
    public function __construct(private string $adminUriBeginning)
    {
    }

    public function getSection(string $uri): SectionInterface
    {
        if (str_starts_with($uri, $this->adminUriBeginning)) {
            return new AdminSection();
        }

        throw new SectionCannotBeResolvedException();
    }
}
