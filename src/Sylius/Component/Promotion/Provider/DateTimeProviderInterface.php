<?php

declare(strict_types=1);

namespace Sylius\Component\Promotion\Provider;

interface DateTimeProviderInterface
{
    public function now(): \DateTimeInterface;
}
