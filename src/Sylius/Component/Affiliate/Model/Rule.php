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

use Sylius\Component\Resource\Model\Rule as BaseRule;

class Rule extends BaseRule implements RuleInterface
{
    /**
     * Associated promotion
     *
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
