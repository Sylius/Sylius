<?php

declare(strict_types=1);

namespace Sylius\Behat;

interface TestAssertionHelperInterface
{
    public function waitUntilAssertionPasses(int $timeout, callable $assertion): void;
}
