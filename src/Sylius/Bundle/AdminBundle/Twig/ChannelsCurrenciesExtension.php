<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ChannelsCurrenciesExtension extends AbstractExtension
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_channels_currencies', [$this, 'getAllCurrencies']),
        ];
    }

    public function getAllCurrencies(): array
    {
        $currencies = [];

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $currencies[$channel->getCode()] = $channel->getBaseCurrency()->getCode();
        }

        return $currencies;
    }
}
