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

namespace Tests\Sylius\Bundle\LocaleBundle\Doctrine\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener;
use Symfony\Contracts\Cache\CacheInterface;

final class LocaleModificationListenerTest extends TestCase
{
    /** @var CacheInterface&MockObject */
    private CacheInterface $cache;

    private LocaleModificationListener $localeModificationListener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = $this->createMock(CacheInterface::class);
        $this->localeModificationListener = new LocaleModificationListener($this->cache);
    }

    public function testInvalidatesCache(): void
    {
        $this->cache->expects(self::once())->method('delete')->with('sylius_locales');

        $this->localeModificationListener->invalidateCachedLocales();
    }
}
