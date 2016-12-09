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
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class PromotionRuleExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var PromotionRuleFactoryInterface
     */
    private $promotionRuleFactory;

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
     * @param PromotionRuleFactoryInterface $promotionRuleFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        PromotionRuleFactoryInterface $promotionRuleFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->promotionRuleFactory = $promotionRuleFactory;
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

        if (ItemTotalRuleChecker::TYPE === $options['type']) {
            /** @var PromotionRuleInterface $promotionRule */
            $promotionRule = $this->promotionRuleFactory->createNew();
            $promotionRule->setType(ItemTotalRuleChecker::TYPE);
            $promotionRule->setConfiguration($this->getItemTotalConfiguration($options['amount']));

            return $promotionRule;
        }

        return $this->promotionRuleFactory->createCartQuantity($options['count']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', CartQuantityRuleChecker::TYPE)
            ->setAllowedValues('type', [
                CartQuantityRuleChecker::TYPE,
                ItemTotalRuleChecker::TYPE,
            ])
            ->setAllowedTypes('type', 'string')
            ->setDefault('count', $this->faker->randomNumber(1))
            ->setAllowedTypes('count', 'integer')
            ->setDefined('amount')
            ->setAllowedTypes('amount', 'array')
        ;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getItemTotalConfiguration(array $options)
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
