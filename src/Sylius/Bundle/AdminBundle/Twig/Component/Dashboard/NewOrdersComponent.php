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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class NewOrdersComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public const DEFAULT_LIMIT = 5;

    public int $limit = self::DEFAULT_LIMIT;

    public string $channelCode;

    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ChannelRepositoryInterface $channelRepository,
    ) {
    }

    /**
     * @return array<OrderInterface>
     */
    #[ExposeInTemplate(name: 'new_orders')]
    public function getNewOrders(): array
    {
        return $this->orderRepository->findLatestInChannel($this->limit, $this->getChannel());
    }

    #[LiveListener('channelChanged')]
    public function changeChannel(#[LiveArg] string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    private function getChannel(): ChannelInterface
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($this->channelCode);

        if ($channel === null) {
            throw new \InvalidArgumentException(sprintf('Channel with code "%s" does not exist.', $this->channelCode));
        }

        return $channel;
    }
}
