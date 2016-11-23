<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Pricing;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\ChannelAndCurrencyPricingConfigurationTransformer;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ChannelAndCurrencyBasedConfigurationType extends AbstractType
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository, RepositoryInterface $currencyRepository)
    {
        $this->channelRepository = $channelRepository;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var ChannelInterface $channel
         * @var CurrencyInterface $currency
         */
        foreach ($this->channelRepository->findAll() as $channel) {
            foreach ($this->currencyRepository->findAll() as $currency) {
                $builder->add($channel->getCode().$options['delimiter'].$currency->getCode(), MoneyType::class, [
                    'label' => sprintf('%s %s', $channel->getName(), $currency->getCode())
                ]);
            }
        }

        $builder->addModelTransformer(new ChannelAndCurrencyPricingConfigurationTransformer($options['delimiter']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'delimiter' => '_x_'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_channel_and_currency_based';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_price_calculator_channel_and_currency_based';
    }
}
