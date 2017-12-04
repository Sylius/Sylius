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

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ZoneFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     *
     * @return ZoneInterface
     */
    public function createTyped(string $type): ZoneInterface;

    /**
     * @param array $membersCodes
     *
     * @return ZoneInterface
     */
    public function createWithMembers(array $membersCodes): ZoneInterface;
}
