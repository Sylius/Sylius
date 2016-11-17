<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Assigner;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface IpAssignerInterface
{
    /**
     * @param OrderInterface $order
     * @param Request $request
     */
    public function assign(OrderInterface $order, Request $request);
}
