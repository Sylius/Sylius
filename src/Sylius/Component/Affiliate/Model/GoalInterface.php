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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\RuleAwareInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface GoalInterface extends RuleAwareInterface, SoftDeletableInterface, TimestampableInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get usage.
     *
     * @return int
     */
    public function getUsed();

    /**
     * Set usage.
     *
     * @param int $used
     */
    public function setUsed($used);

    /**
     * Increment usage.
     */
    public function incrementUsed();

    /**
     * Get start date.
     *
     * @return \DateTime
     */
    public function getStartsAt();

    /**
     * Set start date.
     *
     * @param \DateTime $startsAt
     */
    public function setStartsAt(\DateTime $startsAt = null);

    /**
     * Get end date.
     *
     * @return \DateTime
     */
    public function getEndsAt();

    /**
     * Set end date.
     *
     * @param \DateTime $endsAt
     */
    public function setEndsAt(\DateTime $endsAt = null);

    /**
     * @return Collection|ActionInterface[]
     */
    public function getActions();

    /**
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function hasAction(ActionInterface $action);

    /**
     * @return bool
     */
    public function hasActions();

    /**
     * @param ActionInterface $action
     *
     * @return self
     */
    public function addAction(ActionInterface $action);

    /**
     * @param ActionInterface $action
     *
     * @return self
     */
    public function removeAction(ActionInterface $action);
}
