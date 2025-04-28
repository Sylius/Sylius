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

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Hook\AfterStep;
use Behat\Testwork\Tester\Result\TestResult;

final class BadGatewayContext implements Context
{
    #[AfterStep]
    public function checkForBadGatewayError(AfterStepScope $scope): void
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $exception = $scope->getTestResult()->getException();
        if ($exception && str_contains($exception->getMessage(), '502')) {
            fwrite(\STDERR, "Encountered Bad Gateway (502) error, aborting further tests.\n");
            exit(1);
        }
    }
}
