<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Resolver;

use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Product\Model\VariantInterface;

class RestrictedZoneResolver implements ItemResolverInterface
{
    private $restrictedZoneChecker;

    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data, VariantInterface $variant = null)
    {
        if ($this->restrictedZoneChecker->isRestricted($data)) {
            throw new ItemResolvingException('Selected item is not available in your country.');
        }
    }
}
