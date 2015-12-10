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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMember implements ZoneMemberInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
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
