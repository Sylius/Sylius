<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Addressing\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ZoneMemberInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * @return ZoneInterface
     */
    public function getBelongsTo();

    /**
     * @param ZoneInterface $belongsTo
     */
    public function setBelongsTo(ZoneInterface $belongsTo = null);
}
