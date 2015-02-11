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
    public function getId();
    public function getStatus();
    public function setStatus($status);
    public function getStartTime();
    public function setStartTime(\DateTime $startTime);
    public function getEndTime();
    public function setEndTime(\DateTime $endTime);
    public function getCreatedAt();
    public function setCreatedAt(\DateTime $createdAt);
    public function getUpdatedAt();
    public function setUpdatedAt(\DateTime $updatedAt);
    public function getProfile();
    public function setProfile(ProfileInterface $importProfile);
}
