<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface;
use Sylius\Bundle\CoreBundle\Model\VariantImageInterface;

class VariantImage extends Image implements VariantImageInterface
{
    protected $variant;

    public function getVariant()
    {
        return $this->variant;
    }

    public function setVariant(VariantInterface $variant = null)
    {
        $this->variant = $variant;
    }
}
