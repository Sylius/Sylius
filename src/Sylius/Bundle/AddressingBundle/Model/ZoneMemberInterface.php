<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Zone member interface.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
interface ZoneMemberInterface
{
    /**
     * @return mixed
     */
    public function getId();

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
