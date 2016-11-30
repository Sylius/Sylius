<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Repository;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection|PromotionInterface[]
     */
    public function findActive();

    /**
     * @param string $name
     *
     * @return PromotionInterface[]
     */
    public function findByName($name);
}
