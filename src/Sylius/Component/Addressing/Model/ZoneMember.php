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

class ZoneMember implements ZoneMemberInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var ZoneInterface|null
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getBelongsTo(): ?ZoneInterface
    {
        return $this->belongsTo;
    }

    /**
     * {@inheritdoc}
     */
    public function setBelongsTo(?ZoneInterface $belongsTo): void
    {
        $this->belongsTo = $belongsTo;
    }
}
