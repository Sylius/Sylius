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
     * Get usage limit
     *
     * @return integer
     */
    public function getUsageLimit();

    /**
     * Set usage limit
     *
     * @param integer $usageLimit
     */
    public function setUsageLimit($usageLimit);

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
     * Get deletion date.
     *
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * Set deletion date.
     *
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt = null);

    /**
     * @return Collection|ProvisionInterface[]
     */
    public function getProvisions();

    /**
     * @param ProvisionInterface $provision
     *
     * @return bool
     */
    public function hasProvision(ProvisionInterface $provision);

    /**
     * @return bool
     */
    public function hasProvisions();

    /**
     * @param ProvisionInterface $provision
     *
     * @return self
     */
    public function addProvision(ProvisionInterface $provision);

    /**
     * @param ProvisionInterface $provision
     *
     * @return self
     */
    public function removeProvision(ProvisionInterface $provision);
}
