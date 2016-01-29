<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Templating\Helper\Helper;

class RestrictedZoneHelper extends Helper
{
    /**
     * @var RestrictedZoneCheckerInterface
     */
    private $restrictedZoneChecker;

    /**
     * Constructor.
     *
     * @param RestrictedZoneCheckerInterface $restrictedZoneChecker
     */
    public function __construct(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->restrictedZoneChecker = $restrictedZoneChecker;
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function isRestricted(ProductInterface $product)
    {
        return $this->restrictedZoneChecker->isRestricted($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_restricted_zone';
    }
}
