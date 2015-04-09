<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Support\Model;

/**
 * Interface for the model representing a support topic.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface TopicInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);
}
