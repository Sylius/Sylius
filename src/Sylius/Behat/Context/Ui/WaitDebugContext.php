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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

final class WaitDebugContext implements Context
{
    /** @var array<string, float> */
    private static array $waitTimes = [];

    private static ?ConsoleOutput $output = null;

    public static function recordWait(string $type, float $duration, string $context = ''): void
    {
        $key = $context !== '' ? sprintf('%s [%s]', $type, $context) : $type;
        self::$waitTimes[$key] = (self::$waitTimes[$key] ?? 0) + $duration;
    }

    /** @AfterStep */
    public function afterStep(AfterStepScope $scope): void
    {
        if (empty(self::$waitTimes)) {
            return;
        }

        $output = $this->getOutput();
        $step = $scope->getStep();
        $stepText = sprintf('%s %s', $step->getKeyword(), $step->getText());

        $output->writeln(sprintf('      <fg=cyan>[WAIT]</> Step: <comment>%s</comment>', $stepText));

        foreach (self::$waitTimes as $type => $duration) {
            if ($duration > 0) {
                $output->writeln(sprintf('      <fg=cyan>[WAIT]</> <info>%s:</info> <fg=yellow>%.2f ms</>', $type, $duration));
            }
        }

        self::$waitTimes = [];
    }

    private function getOutput(): ConsoleOutput
    {
        if (self::$output === null) {
            self::$output = new ConsoleOutput();
            $styleComment = new OutputFormatterStyle('yellow');
            $styleInfo = new OutputFormatterStyle('cyan');
            self::$output->getFormatter()->setStyle('comment', $styleComment);
            self::$output->getFormatter()->setStyle('info', $styleInfo);
        }

        return self::$output;
    }
}
