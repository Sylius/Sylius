<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Exception\ElementNotFoundException;

final class JavaScriptTestHelper implements JavaScriptTestHelperInterface
{
    private $sleepTime;

    public function __construct(int $sleepTime)
    {
        $this->sleepTime = $sleepTime;
    }

    public function waitUntilNotificationPopups(int $timeout, callable $assertion): void
    {
        $start = microtime(true);
        $end = $start + $timeout;

        do {
            try {
                $assertion();
            } catch (ElementNotFoundException $exception) {
                usleep($this->sleepTime);

                continue;
            }

            return;
        } while (microtime(true) < $end);

        throw new \InvalidArgumentException('Time has run out and the assertion has not passed yet.');
    }

    public function waitUntilAssertionPasses(int $timeout, callable $assertion): void
    {
        $start = microtime(true);
        $end = $start + $timeout;

        do {
            try {
                $assertion();
            } catch (\InvalidArgumentException $exception) {
                usleep($this->sleepTime);

                continue;
            }

            return;
        } while (microtime(true) < $end);

        throw new \InvalidArgumentException('Time has run out and the assertion has not passed yet.');;
    }
}
