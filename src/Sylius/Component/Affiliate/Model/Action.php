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

use Sylius\Component\Resource\Model\Action as BaseAction;

class Action extends BaseAction implements ActionInterface
{
    /**
     * @var GoalInterface
     */
    protected $goal;

    /**
     * {@inheritdoc}
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * {@inheritdoc}
     */
    public function setGoal(GoalInterface $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }
}
