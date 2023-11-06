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

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class PaymentRequest implements PaymentRequestInterface
{
    use TimestampableTrait;

    protected ?string $hash = null;

    protected ?PaymentMethodInterface $method = null;

    protected ?PaymentInterface $payment = null;

    protected string $state = PaymentRequestInterface::STATE_NEW;

    protected string $type = PaymentRequestInterface::DATA_TYPE_CAPTURE;

    protected mixed $data = null;

    /** @var mixed[] */
    protected array $details = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->hash;
    }

    public function getMethod(): ?PaymentMethodInterface
    {
        return $this->method;
    }

    public function setMethod(?PaymentMethodInterface $method): void
    {
        $this->method = $method;
    }

    public function getPayment(): ?PaymentInterface
    {
        return $this->payment;
    }

    public function setPayment(?PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
