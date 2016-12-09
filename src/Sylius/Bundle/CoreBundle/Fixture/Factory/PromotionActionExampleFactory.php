<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class PromotionActionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var PromotionActionFactoryInterface
     */
    private $promotionActionFactory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param PromotionActionFactoryInterface $promotionActionFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        PromotionActionFactoryInterface $promotionActionFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->promotionActionFactory = $promotionActionFactory;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        if (FixedDiscountPromotionActionCommand::TYPE === $options['type']) {
            /** @var PromotionActionInterface $promotionAction */
            $promotionAction = $this->promotionActionFactory->createNew();
            $promotionAction->setType(FixedDiscountPromotionActionCommand::TYPE);
            $promotionAction->setConfiguration($this->getFixedDiscountConfiguration($options['amount']));

            return $promotionAction;
        }

        if (ShippingPercentageDiscountPromotionActionCommand::TYPE === $options['type']) {
            return $this->promotionActionFactory->createShippingPercentageDiscount($options['percentage']);
        }

        return $this->promotionActionFactory->createPercentageDiscount($options['percentage']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', PercentageDiscountPromotionActionCommand::TYPE)
            ->setAllowedValues('type', [
                FixedDiscountPromotionActionCommand::TYPE,
                PercentageDiscountPromotionActionCommand::TYPE,
                ShippingPercentageDiscountPromotionActionCommand::TYPE,
            ])
            ->setAllowedTypes('type', 'string')
            ->setDefault('percentage', $this->faker->randomNumber(2))
            ->setAllowedTypes('percentage', ['integer', 'float'])
            ->setNormalizer('percentage', function (Options $options, $percentage) {
                return $percentage / 100;
            })
            ->setDefined('amount')
            ->setAllowedTypes('amount', 'array')
        ;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getFixedDiscountConfiguration(array $options)
    {
        $configuration = [];

        if (!isset($options)) {
            $channels = $this->channelRepository->findAll();

            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                $configuration[$channel->getCode()] = ['amount' => $this->faker->randomNumber(4)];
            }

            return $configuration;
        }

        foreach ($options as $channelCode => $amount) {
            $configuration[$channelCode] = ['amount' => $amount * 100];
        }

        return $configuration;
    }
}
