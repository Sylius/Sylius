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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface;
use Sylius\Bundle\CartBundle\Entity\CartItem;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;

/**
 * Order item with the product variant attached.
 */
class OrderItem extends CartItem implements OrderItemInterface
{
}
