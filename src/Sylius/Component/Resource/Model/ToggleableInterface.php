<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Model;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ToggleableInterface
{
    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    public function enable();

    public function disable();
}
