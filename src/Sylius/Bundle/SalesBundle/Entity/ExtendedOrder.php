<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\SalesBundle\Model\ExtendedOrder as BaseExtendedOrder;

/**
 * Default extended order entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ExtendedOrder extends BaseExtendedOrder
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->items = new ArrayCollection;
    }
}
