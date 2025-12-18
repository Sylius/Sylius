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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class CurrencySwitcherComponent
{
    use HookableComponentTrait;

    public function __construct(
        protected ChannelContextInterface $channelContext,
        protected CurrencyContextInterface $currencyContext,
    ) {
    }

    #[ExposeInTemplate('active_currency')]
    public function activeCurrency(): string
    {
        return $this->currencyContext->getCurrencyCode();
    }

    /**
     * @return array<string>
     */
    #[ExposeInTemplate('available_currencies')]
    public function availableCurrencies(): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return array_map(
            fn (CurrencyInterface $currency) => $currency->getCode(),
            $channel->getCurrencies()->toArray(),
        );
    }
}
