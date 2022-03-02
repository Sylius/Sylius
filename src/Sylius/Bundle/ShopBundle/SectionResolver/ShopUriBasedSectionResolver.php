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

namespace Sylius\Bundle\ShopBundle\SectionResolver;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class ShopUriBasedSectionResolver implements UriBasedSectionResolverInterface
{
    /** @var string */
    private $shopCustomerAccountUri;

    public function __construct(string $shopCustomerAccountUri = 'account')
    {
        $this->shopCustomerAccountUri = $shopCustomerAccountUri;
    }

    public function getSection(string $uri): SectionInterface
    {
        if (str_contains($uri, $this->shopCustomerAccountUri)) {
            return new ShopCustomerAccountSubSection();
        }

        return new ShopSection();
    }
}
