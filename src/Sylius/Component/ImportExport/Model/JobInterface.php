<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Model;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface JobInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @return \DateTime
     */
    public function getStartTime();

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime);

    /**
     * @return \DateTime
     */
    public function getEndTime();

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime(\DateTime $endTime);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return ProfileInterface
     */
    public function getProfile();

    /**
     * @param ProfileInterface $importProfile
     */
    public function setProfile(ProfileInterface $importProfile);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @param array $metadata]
     */
    public function setMetadata(array $metadata);

    /**
     * @param array $metadata
     */
    public function addMetadata(array $metadata);
}
