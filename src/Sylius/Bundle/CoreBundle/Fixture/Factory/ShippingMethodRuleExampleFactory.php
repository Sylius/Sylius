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

use Sylius\Component\Core\Factory\ShippingMethodRuleFactoryInterface;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingMethodRuleExampleFactory extends AbstractExampleFactory
{
    /** @var ShippingMethodRuleFactoryInterface */
    private $shippingMethodRuleFactory;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(ShippingMethodRuleFactoryInterface $shippingMethodRuleFactory)
    {
        $this->shippingMethodRuleFactory = $shippingMethodRuleFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ShippingMethodRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ShippingMethodRuleInterface $shippingMethodRule */
        $shippingMethodRule = $this->shippingMethodRuleFactory->createNew();
        $shippingMethodRule->setType($options['type']);
        $shippingMethodRule->setConfiguration($options['configuration']);

        return $shippingMethodRule;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', TotalWeightGreaterThanOrEqualRuleChecker::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [
                'weight' => $this->faker->randomNumber(1),
            ])
            ->setNormalizer('configuration', function (Options $options, array $configuration): array {
                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] = (int) ($configuration[$channelCode]['amount'] * 100);
                    }
                }

                return $configuration;
            })
        ;
    }
}
