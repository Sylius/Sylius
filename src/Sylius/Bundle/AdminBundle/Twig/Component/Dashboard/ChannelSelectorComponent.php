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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Dashboard;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class ChannelSelectorComponent
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public string $channelCode = '';

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(private readonly ChannelRepositoryInterface $channelRepository)
    {
    }

    /**
     * @return array<string, ChannelInterface>
     */
    #[ExposeInTemplate(name: 'channels')]
    public function getChannels(): array
    {
        return $this->channelRepository->findEnabled();
    }

    #[LiveAction]
    public function changeChannel(
        #[LiveArg] string $channelCode,
    ): void {
        $this->channelCode = $channelCode;

        $this->emit('channelChanged', ['channelCode' => $channelCode]);
    }

    #[ExposeInTemplate(name: 'channel_name')]
    public function getChannelName(): string
    {
        $channel = $this->channelRepository->findOneByCode($this->channelCode);

        return $channel ? $channel->getName() : '';
    }
}
