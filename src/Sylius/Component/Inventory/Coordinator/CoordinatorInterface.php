<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Coordinator;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;

/**
 * This service is responsible for getting packages from all available stock locations.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CoordinatorInterface
{
    /**
     * Get all packages for given inventory subject and inventory units.
     *
     * @param InventorySubjectInterface
     *
     * @return PackageInterface[]
     */
    public function getPackages(InventorySubjectInterface $subject);
}
