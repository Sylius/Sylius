<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Application\Command;

final class FooCommand
{
    private string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }
}
