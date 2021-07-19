<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Application\Command;

final class FooCommand
{
    /** @var string */
    private $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }
}
