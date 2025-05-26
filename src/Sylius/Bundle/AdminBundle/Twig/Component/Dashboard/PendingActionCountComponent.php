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

use Sylius\Bundle\AdminBundle\PendingAction\Provider\PendingActionCountProviderInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class PendingActionCountComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    public string $channelCode;

    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        protected readonly ChannelRepositoryInterface $channelRepository,
        protected readonly PendingActionCountProviderInterface $pendingActionProvider,
    ) {
    }

    #[ExposeInTemplate('count')]
    public function getCount(): int
    {
        return $this->pendingActionProvider->count($this->getChannel());
    }

    #[ExposeInTemplate('channel')]
    public function getChannel(): ChannelInterface
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($this->channelCode);

        if ($channel === null) {
            throw new \InvalidArgumentException(sprintf('Channel with code "%s" does not exist.', $this->channelCode));
        }

        return $channel;
    }

    #[LiveListener(ChannelSelectorComponent::SYLIUS_ADMIN_CHANNEL_CHANGED)]
    public function changeChannel(#[LiveArg] string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }
}
