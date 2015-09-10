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

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Contact topic translation interface.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
interface TopicTranslationInterface extends ResourceInterface
{
    /**
     * Get topic title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set topic title.
     *
     * @param string $title
     */
    public function setTitle($title);
}
