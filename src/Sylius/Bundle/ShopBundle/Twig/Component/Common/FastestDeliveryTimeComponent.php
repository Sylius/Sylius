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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Bundle\CoreBundle\Provider\DeliveryTimeProviderInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class FastestDeliveryTimeComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    private const TRANS_EXACT = 'sylius.ui.delivery_time.exact';

    private const TRANS_RANGE = 'sylius.ui.delivery_time.range';

    public function __construct(
        private readonly DeliveryTimeProviderInterface $deliveryTimeProvider,
        private readonly ChannelContextInterface $channelContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[ExposeInTemplate('delivery_time')]
    public function deliveryTime(): ?string
    {
        $channel = $this->channelContext->getChannel();
        $best = $this->deliveryTimeProvider->provide($channel);

        if ($best === null) {
            return null;
        }

        if ($best['minimumDays'] === $best['maximumDays']) {
            return $this->translator->trans(self::TRANS_EXACT, ['%count%' => $best['minimumDays']]);
        }

        return $this->translator->trans(self::TRANS_RANGE, ['%min%' => $best['minimumDays'], '%max%' => $best['maximumDays']]);
    }
}
