<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;

class SyliusRestrictedZoneExtension extends Twig_Extension
{
    private $restrictedZoneChecker;

    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
    }

    public function getFunctions()
    {
        return array(
            'sylius_is_restricted' => new Twig_Function_Method($this, 'isRestricted'),
        );
    }

    public function isRestricted(ProductInterface $product)
    {
        return $this->restrictedZoneChecker->isRestricted($product);
    }

    public function getName()
    {
        return 'sylius_restricted_zone';
    }
}
