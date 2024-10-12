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

namespace Sylius\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

final class DebugContext implements Context
{
    /** @var array<int, array{type: DebugErrorType, error: string}> */
    private array $errorStack = [];

    public function __construct(private readonly ResponseCheckerInterface $responseChecker)
    {
    }

    /** @AfterStep */
    public function afterStep(AfterStepScope $scope): void
    {
        $debugErrors = $this->responseChecker->getDebugErrors();

        if (empty($debugErrors)) {
            return;
        }

        $this->errorStack[] = [
            'step' => $scope->getStep()->getText(),
            'type' => DebugErrorType::API_RESPONSE,
            'error' => $debugErrors,
        ];

        $this->responseChecker->cleanErrors();
    }

    /** @AfterScenario */
    public function afterScenario(): void
    {
        if (!empty($this->errorStack)) {
            $output = new ConsoleOutput();
            $styleKey = new OutputFormatterStyle('cyan');
            $styleValue = new OutputFormatterStyle('green');
            $output->getFormatter()->setStyle('key', $styleKey);
            $output->getFormatter()->setStyle('value', $styleValue);

            $json = json_encode($this->errorStack, \JSON_PRETTY_PRINT);

            $formattedJson = preg_replace('/"([^"]+)":/', '<key>"$1"</key>:', $json);
            $formattedJson = preg_replace('/: "([^"]+)"/', ': <value>"$1"</value>', $formattedJson);

            $output->writeln($formattedJson);
        }
    }
}
