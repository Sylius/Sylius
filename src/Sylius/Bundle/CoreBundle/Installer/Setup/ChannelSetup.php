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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class ChannelSetup implements ChannelSetupInterface
{
    /**
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     * @param FactoryInterface<ChannelInterface> $channelFactory
     */
    public function __construct(
        private RepositoryInterface $channelRepository,
        private FactoryInterface $channelFactory,
        private ObjectManager $channelManager,
    ) {
    }

    public function setup(
        LocaleInterface $locale,
        CurrencyInterface $currency,
        CountryInterface $country,
        ZoneInterface $zone,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): void {
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
        $channel->addCountry($country);

        $question = new ConfirmationQuestion('Assign created zone as default tax zone? (yes/no) [no]: ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            $channel->setDefaultTaxZone($zone);
        }

        $this->channelManager->flush();
    }
}
