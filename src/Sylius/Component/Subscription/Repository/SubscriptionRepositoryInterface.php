<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Repository;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Subscription\Model\SubscriptionInterface;

/**
 * Subscription repository interface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionRepositoryInterface
{
    /**
     * Get subscriptions scheduled for processing.
     *
     * @param \DateTime $date
     * @return Collection|SubscriptionInterface[]
     */
    public function findScheduled(\DateTime $date = null);

    /**
     * Get active subscriptions for user.
     *
     * @param UserInterface $user
     * @return Collection|SubscriptionInterface[]
     */
    public function findByUser(UserInterface $user);
}
