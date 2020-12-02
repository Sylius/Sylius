<?php

declare(strict_types=1);

namespace Sylius\Behat;

final class TestAssertionHelper implements TestAssertionHelperInterface
{
    public function waitUntilAssertionPasses(int $timeout, callable $assertion): void
    {
        $start = microtime(true);
        $end = $start + $timeout;

        do {
            try {
                $assertion();
            } catch (\InvalidArgumentException $exception) {
                usleep(100000);

                continue;
            }

            return;
        } while (microtime(true) < $end);

        throw new \InvalidArgumentException('Time has run out and the assertion has not passed yet.');
    }
}
