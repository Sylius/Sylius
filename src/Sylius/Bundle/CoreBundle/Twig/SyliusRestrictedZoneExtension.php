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

use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;

class SyliusRestrictedZoneExtension extends \Twig_Extension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_is_restricted', array($this, 'isRestricted')),
        );
    }

    /**
     * @param ProductInterface $product
     *
     * @return boolean
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
