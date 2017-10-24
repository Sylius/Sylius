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

namespace Sylius\Component\Locale\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\Intl\Intl;

class Locale implements LocaleInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

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
    public function getName(?string $locale = null): ?string
    {
        return Intl::getLocaleBundle()->getLocaleName($this->getCode(), $locale);
    }
}
