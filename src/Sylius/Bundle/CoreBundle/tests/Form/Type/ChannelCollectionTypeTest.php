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

namespace Tests\Sylius\Bundle\CoreBundle\Form\Type;

use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Service\ResetInterface;

final class ChannelCollectionTypeTest extends TypeTestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ChannelCollectionType $type;

    public function testItBuildsAnEntryForEveryChannel(): void
    {
        $this->channelRepository->method('findAll')->willReturn([$this->channel('WEB_US')]);

        $form = $this->factory->create(ChannelCollectionType::class, null, ['entry_type' => TextType::class]);

        $this->assertTrue($form->has('WEB_US'));
    }

    /**
     * configureOptions() runs once per ResolvedFormType and the registry keeps
     * that instance for as long as the process lives, so the entries have to be
     * a lazy option - a value would be computed once and every later form in
     * the process would be built from the channels of the first one.
     */
    public function testItBuildsAnEntryForAChannelCreatedSinceTheLastReset(): void
    {
        $existing = $this->channel('WEB_US');
        $created = $this->channel('MOBILE');

        $calls = 0;
        $this->channelRepository->method('findAll')->willReturnCallback(
            function () use (&$calls, $existing, $created): array {
                return ++$calls === 1 ? [$existing] : [$existing, $created];
            },
        );

        $this->factory->create(ChannelCollectionType::class, null, ['entry_type' => TextType::class]);

        //  Asserted rather than called straight away: without the reset the
        //  type is not resettable at all, and a plain call would end the run
        //  with a fatal instead of a failure naming what is missing.
        $this->assertInstanceOf(
            ResetInterface::class,
            $this->type,
            'The type must be resettable, otherwise nothing refreshes its channel list between requests.',
        );
        $this->type->reset();
        $form = $this->factory->create(ChannelCollectionType::class, null, ['entry_type' => TextType::class]);

        $this->assertTrue(
            $form->has('MOBILE'),
            'The form was built from the channel list of a previous request.',
        );
    }

    /**
     * The list is kept for the rest of the request on purpose. Resolving it on
     * every form would turn one query into one per form, and a page like
     * variant generation builds one of these per variant.
     */
    public function testItQueriesTheChannelsOnlyOnceBetweenResets(): void
    {
        $this->channelRepository->expects($this->once())->method('findAll')->willReturn([$this->channel('WEB_US')]);

        $this->factory->create(ChannelCollectionType::class, null, ['entry_type' => TextType::class]);
        $this->factory->create(ChannelCollectionType::class, null, ['entry_type' => TextType::class]);
    }

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->type = new ChannelCollectionType($this->channelRepository);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        return [new PreloadedExtension([$this->type], [])];
    }

    private function channel(string $code): ChannelInterface&MockObject
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn($code);

        return $channel;
    }
}
