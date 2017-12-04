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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionRuleExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var PromotionRuleFactoryInterface
     */
    private $promotionRuleFactory;

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
     */
    public function __construct(PromotionRuleFactoryInterface $promotionRuleFactory)
    {
        $this->promotionRuleFactory = $promotionRuleFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): PromotionRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PromotionRuleInterface $promotionRule */
        $promotionRule = $this->promotionRuleFactory->createNew();
        $promotionRule->setType($options['type']);
        $promotionRule->setConfiguration($options['configuration']);

        return $promotionRule;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', CartQuantityRuleChecker::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [
                'count' => $this->faker->randomNumber(1),
            ])
            ->setNormalizer('configuration', function (Options $options, $configuration): array {
                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] *= 100;
                    }
                }

                return $configuration;
            })
        ;
    }
}
