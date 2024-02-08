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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ChannelSetup implements ChannelSetupInterface
{
    public function __construct(
        private RepositoryInterface $channelRepository,
        private FactoryInterface $channelFactory,
        private ObjectManager $channelManager,
    ) {
    }

    public function setup(LocaleInterface $locale, CurrencyInterface $currency): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy([]);

        if (null === $channel) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelFactory->createNew();
            $channel->setCode('default');
            $channel->setName('Default');
            $channel->setTaxCalculationStrategy('order_items_based');

            $this->channelManager->persist($channel);
        }

        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);
        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);

        $this->channelManager->flush();
    }
}
