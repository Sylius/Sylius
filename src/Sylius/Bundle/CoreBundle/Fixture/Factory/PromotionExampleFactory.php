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

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class PromotionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $promotionFactory;

    /**
     * @var ExampleFactoryInterface
     */
    private $promotionRuleExampleFactory;

    /**
     * @var ExampleFactoryInterface
     */
    private $promotionActionExampleFactory;

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
     * @param FactoryInterface $promotionFactory
     * @param ExampleFactoryInterface $promotionRuleExampleFactory
     * @param ExampleFactoryInterface $promotionActionExampleFactory
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        FactoryInterface $promotionFactory,
        ExampleFactoryInterface $promotionRuleExampleFactory,
        ExampleFactoryInterface $promotionActionExampleFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->promotionFactory = $promotionFactory;
        $this->promotionRuleExampleFactory = $promotionRuleExampleFactory;
        $this->promotionActionExampleFactory = $promotionActionExampleFactory;
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

        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionFactory->createNew();
        $promotion->setCode($options['code']);
        $promotion->setName($options['name']);
        $promotion->setDescription($options['description']);
        $promotion->setCouponBased($options['coupon_based']);
        $promotion->setUsageLimit($options['usage_limit']);
        $promotion->setExclusive($options['exclusive']);
        $promotion->setPriority($options['priority']);

        if (isset($options['starts_at'])) {
            $promotion->setStartsAt(new \DateTime($options['starts_at']));
        }

        if (isset($options['ends_at'])) {
            $promotion->setEndsAt(new \DateTime($options['ends_at']));
        }

        foreach ($options['channels'] as $channel) {
            $promotion->addChannel($channel);
        }

        foreach ($options['rules'] as $rule) {
            /** @var PromotionRuleInterface $promotionRule */
            $promotionRule = $this->promotionRuleExampleFactory->create($rule);
            $promotion->addRule($promotionRule);
        }

        foreach ($options['actions'] as $action) {
            /** @var PromotionActionInterface $promotionAction */
            $promotionAction = $this->promotionActionExampleFactory->create($action);
            $promotion->addAction($promotionAction);
        }

        return $promotion;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('name', $this->faker->words(3, true))
            ->setDefault('description', $this->faker->sentence())
            ->setDefault('usage_limit', null)
            ->setDefault('coupon_based', false)
            ->setDefault('exclusive', $this->faker->boolean(25))
            ->setDefault('priority', 0)
            ->setDefault('starts_at', null)
            ->setAllowedTypes('starts_at', ['null', 'string'])
            ->setDefault('ends_at', null)
            ->setAllowedTypes('ends_at', ['null', 'string'])
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefined('rules')
            ->setNormalizer('rules', function (Options $options, array $rules) {
                if (empty($rules)) {
                    return [[]];
                }

                return $rules;
            })
            ->setDefined('actions')
            ->setNormalizer('actions', function (Options $options, array $actions) {
                if (empty($actions)) {
                    return [[]];
                }

                return $actions;
            })
        ;
    }
}
