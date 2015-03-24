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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\RuleInterface as BaseRuleInterface;

class Goal implements GoalInterface
{
    /**
     * Id.
     *
     * @var int
     */
    protected $id;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Description.
     *
     * @var string
     */
    protected $description;

    /**
     * Number of times this coupon has been used.
     *
     * @var int
     */
    protected $used = 0;

    /**
     * Associated rules.
     *
     * @var Collection|RuleInterface[]
     */
    protected $rules;

    /**
     * Associated actions.
     *
     * @var Collection|ActionInterface[]
     */
    protected $actions;

    /**
     * Start date.
     *
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * End date.
     *
     * @var \DateTime
     */
    protected $endsAt;

    /**
     * Last time updated
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Creation date
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Deletion time.
     *
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementUsed()
    {
        ++$this->used;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRules()
    {
        return !$this->rules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(BaseRuleInterface $rule)
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(BaseRuleInterface $rule)
    {
        if (!$this->hasRule($rule)) {
            $rule->setGoal($this);
            $this->rules->add($rule);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(BaseRuleInterface $rule)
    {
        if ($this->hasRule($rule)) {
            $rule->setGoal(null);
            $this->rules->removeElement($rule);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActions()
    {
        return !$this->actions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(ActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $action->setGoal($this);
            $this->actions->add($action);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(ActionInterface $action)
    {
        if ($this->hasAction($action)) {
            $action->setGoal(null);
            $this->actions->removeElement($action);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(\DateTime $startsAt = null)
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(\DateTime $endsAt = null)
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
