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
 * Contact category translation interface.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
interface CategoryTranslationInterface
{
    /**
     * Get id.
     */
    public function getId();

    /**
     * Get category title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set category title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);
}
