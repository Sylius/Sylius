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
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface VersionedInterface
{
    /**
     * @return int|null
     */
    public function getVersion(): ?int;

    /**
     * @param int|null $version
     */
    public function setVersion(?int $version): void;
}
