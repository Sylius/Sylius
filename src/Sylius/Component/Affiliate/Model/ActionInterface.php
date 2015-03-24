<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;

use Sylius\Component\Resource\Model\ActionInterface as BaseActionInterface;

interface ActionInterface extends BaseActionInterface
{
    /**
     * Get goal.
     *
     * @return GoalInterface
     */
    public function getGoal();

    /**
     * Set goal.
     *
     * @param GoalInterface $goal
     */
    public function setGoal(GoalInterface $goal = null);
}
