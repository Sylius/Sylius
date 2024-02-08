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

namespace Sylius\Bundle\CoreBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Twig\ChannelUrlExtension;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;

final class ChannelUrlExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_channel_url_with_channel_hostname(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://shop.example.com'));
        $channel = new Channel();
        $channel->setHostname('example.com');
        $extension = new ChannelUrlExtension($this->createMock(ChannelContextInterface::class), new UrlHelper($stack));

        $url = $extension->generateChannelUrl('/en_US/cart/', $channel);

        self::assertEquals('https://example.com/en_US/cart/', $url);
    }

    public function testGenerateChannelUrlUnsecured(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://shop.example.com'));
        $channel = new Channel();
        $channel->setHostname('example.com');
        $extension = new ChannelUrlExtension($this->createMock(ChannelContextInterface::class), new UrlHelper($stack), true);

        $url = $extension->generateChannelUrl('/en_US/cart/', $channel);

        self::assertEquals('http://example.com/en_US/cart/', $url);
    }

    public function testGenerateChannelUrlUsingChannelContext(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://shop.example.com'));
        $channel = new Channel();
        $channel->setHostname('example.com');
        $channelContext = $this->createConfiguredMock(ChannelContextInterface::class, [
            'getChannel' => $channel,
        ]);
        $extension = new ChannelUrlExtension($channelContext, new UrlHelper($stack));

        $url = $extension->generateChannelUrl('/en_US/cart/');

        self::assertEquals('https://example.com/en_US/cart/', $url);
    }

    public function testGenerateChannelUrlUsingRequestHostname(): void
    {
        $stack = new RequestStack();
        $stack->push(Request::create('https://shop.example.com'));
        $channelContext = $this->createMock(ChannelContextInterface::class);
        $extension = new ChannelUrlExtension($channelContext, new UrlHelper($stack));

        $url = $extension->generateChannelUrl('/en_US/cart/');

        self::assertEquals('https://shop.example.com/en_US/cart/', $url);
    }
}
