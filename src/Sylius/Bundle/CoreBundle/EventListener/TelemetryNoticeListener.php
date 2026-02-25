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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TelemetryNoticeListener
{
    private const CACHE_KEY = 'sylius_telemetry_notice_shown';

    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        try {
            if ($event->getExitCode() !== 0) {
                return;
            }

            if ($this->isRunningInComposerScript()) {
                return;
            }

            $item = $this->cache->getItem(self::CACHE_KEY);
            if ($item->isHit()) {
                return;
            }

            $io = new SymfonyStyle($event->getInput(), $event->getOutput());
            $io->newLine();
            $io->block('Sylius Telemetry', null, 'bg=cyan;fg=black', '  ', true);
            $io->text([
                'Sylius collects <comment>anonymous usage data</> to help improve the platform.',
                'No personal or sensitive information is collected.',
                '',
                '  * Learn more: <comment>https://docs.sylius.com/the-book/configuration/telemetry</>',
            ]);
            $io->newLine();

            $item->set(true);
            $this->cache->save($item);
        } catch (\Throwable) {
        }
    }

    private function isRunningInComposerScript(): bool
    {
        return getenv('COMPOSER_BINARY') !== false || isset($_SERVER['COMPOSER_BINARY']);
    }
}
