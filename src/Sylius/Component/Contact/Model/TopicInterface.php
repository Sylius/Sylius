<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Contact\Model;

use Sylius\Component\Resource\Model\GetIdInterface;

/**
 * Interface for the model representing a contact topic.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface TopicInterface extends GetIdInterface
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
