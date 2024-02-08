<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\Intl\Currencies;

class Currency implements CurrencyInterface, \Stringable
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return (string) $this->getCode();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        if (null === $code = $this->getCode()) {
            return null;
        }

        return Currencies::getName($code);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
