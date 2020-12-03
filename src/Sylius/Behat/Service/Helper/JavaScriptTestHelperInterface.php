<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

interface JavaScriptTestHelperInterface
{
    public function waitUntilNotificationPopups(int $timeout, callable $assertion): void;

    public function waitUntilAssertionPasses(int $timeout, callable $assertion): void;

    public function waitUntilPageOpens(int $timeout, callable $assertion): void;
}
