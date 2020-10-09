<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\SectionResolver;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminApiUriBasedSectionResolver implements UriBasedSectionResolverInterface
{
    /** @var string */
    private $adminApiUriBeginning;

    public function __construct(string $adminApiUriBeginning)
    {
        $this->adminApiUriBeginning = $adminApiUriBeginning;
    }

    public function getSection(string $uri): SectionInterface
    {
        if (0 === strpos($uri, $this->adminApiUriBeginning)) {
            return new AdminApiSection();
        }

        throw new SectionCannotBeResolvedException();
    }
}
