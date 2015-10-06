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

class Rule implements RuleInterface
{
    /**
     * Id.
     *
     * @var int
     */
    protected $id;

    /**
     * Associated goal
     *
     * @var GoalInterface
     */
    protected $goal;

    /**
     * Type.
     *
     * @var string
     */
    protected $type;

    /**
     * Configuration.
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

}
