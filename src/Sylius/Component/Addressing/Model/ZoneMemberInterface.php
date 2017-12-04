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

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ZoneMemberInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * @return ZoneInterface|null
     */
    public function getBelongsTo(): ?ZoneInterface;

    /**
     * @param ZoneInterface|null $belongsTo
     */
    public function setBelongsTo(?ZoneInterface $belongsTo): void;
}
