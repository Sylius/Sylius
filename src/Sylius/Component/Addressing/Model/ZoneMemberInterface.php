<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\GetIdInterface;

/**
 * Zone member interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ZoneMemberInterface extends GetIdInterface
{
    /**
     * @return ZoneInterface
     */
    public function getBelongsTo();

    /**
     * @param ZoneInterface $belongsTo
     *
     * @return ZoneMemberInterface
     */
    public function setBelongsTo(ZoneInterface $belongsTo = null);

    /**
     * Gets zone member name.
     *
     * @return string
     */
    public function getName();
}
