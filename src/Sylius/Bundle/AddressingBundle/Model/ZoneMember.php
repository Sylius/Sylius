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
 * Default zone member model.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
abstract class ZoneMember implements ZoneMemberInterface
{
    /**
     * Zone member id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Zone member belongs to.
     *
     * @var ZoneInterface
     */
    protected $belongsTo;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getBelongsTo()
    {
        return $this->belongsTo;
    }

    /**
     * {@inheritdoc}
     */
    public function setBelongsTo(ZoneInterface $belongsTo = null)
    {
        $this->belongsTo = $belongsTo;

        return $this;
    }
}
