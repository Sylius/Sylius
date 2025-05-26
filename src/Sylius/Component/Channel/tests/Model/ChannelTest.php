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

namespace Tests\Sylius\Component\Channel\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Channel\Model\ChannelInterface;

class ChannelTest extends TestCase
{
    private Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channel = new Channel();
    }

    public function testShouldImplementChannelInterface(): void
    {
        self::assertInstanceOf(ChannelInterface::class, $this->channel);
    }

    public function testShouldHasNoIdByDefault(): void
    {
        self::assertNull($this->channel->getId());
    }

    public function testShouldHasNoCodeByDefault(): void
    {
        self::assertNull($this->channel->getCode());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->channel->setCode('mobile');
        self::assertSame('mobile', $this->channel->getCode());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        self::assertNull($this->channel->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->channel->setName('Mobile Store');
        self::assertSame('Mobile Store', $this->channel->getName());
    }

    public function testShouldHasNoColorByDefault(): void
    {
        self::assertNull($this->channel->getColor());
    }

    public function testColorShouldBeMutable(): void
    {
        $this->channel->setColor('#1abb9c');
        self::assertSame('#1abb9c', $this->channel->getColor());
    }

    public function testShouldBeEnabledByDefault(): void
    {
        self::assertTrue($this->channel->isEnabled());
    }

    public function testCanBeDisabled(): void
    {
        $this->channel->setEnabled(false);
        self::assertFalse($this->channel->isEnabled());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->channel->getCreatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->channel->setCreatedAt($date);
        self::assertSame($date, $this->channel->getCreatedAt());
    }

    public function testShouldHasNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->channel->getUpdatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->channel->setUpdatedAt($date);
        self::assertSame($date, $this->channel->getUpdatedAt());
    }
}
