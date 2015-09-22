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

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
abstract class ZoneMember implements ZoneMemberInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
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
    }
}
