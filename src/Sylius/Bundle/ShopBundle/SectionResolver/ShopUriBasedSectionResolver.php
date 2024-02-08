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
        if (strpos($uri, $this->shopCustomerAccountUri) !== false) {
            return new ShopCustomerAccountSubSection();
        }

        return new ShopSection();
    }
}
