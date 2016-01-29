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

use Sylius\Bundle\CoreBundle\Templating\Helper\RestrictedZoneHelper;
use Sylius\Component\Core\Model\ProductInterface;

class RestrictedZoneExtension extends \Twig_Extension
{
    /**
     * @var RestrictedZoneHelper
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param RestrictedZoneHelper $helper
     */
    public function __construct(RestrictedZoneHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_is_restricted', [$this, 'isRestricted']),
        ];
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function isRestricted(ProductInterface $product)
    {
        return $this->helper->isRestricted($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_restricted_zone';
    }
}
