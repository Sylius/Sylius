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

namespace Tests\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProvider;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class FakeChannelCodeProviderTest extends TestCase
{
    private FakeChannelCodeProvider $fakeChannelCodeProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeChannelCodeProvider = new FakeChannelCodeProvider();
    }

    public function testImplementsAChannelCodeProviderInterface(): void
    {
        self::assertInstanceOf(FakeChannelCodeProviderInterface::class, $this->fakeChannelCodeProvider);
    }

    public function testReturnsFakeChannelCodeFromQueryString(): void
    {
        $request = new Request(query: ['_channel_code' => 'channel_code_form_get']);

        self::assertSame('channel_code_form_get', $this->fakeChannelCodeProvider->getCode($request));
    }

    public function testReturnsFakeChannelCodeFromCookieIfThereIsNoneInQueryString(): void
    {
        $request = new Request(
            query: ['_channel_code' => null],
            cookies: ['_channel_code' => 'channel_code_form_cookie'],
        );

        self::assertSame('channel_code_form_cookie', $this->fakeChannelCodeProvider->getCode($request));
    }

    public function testReturnsNullChannelCodeIfNoFakeChannelCodeWasFound(): void
    {
        $request = new Request(
            query: ['_channel_code' => null],
            cookies: ['_channel_code' => null],
        );

        self::assertNull($this->fakeChannelCodeProvider->getCode($request));
    }
}
