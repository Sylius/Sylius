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

use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
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
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param PromotionActionFactoryInterface $promotionActionFactory
     */
    public function __construct(PromotionActionFactoryInterface $promotionActionFactory)
    {
        $this->promotionActionFactory = $promotionActionFactory;

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

        /** @var PromotionActionInterface $promotionAction */
        $promotionAction = $this->promotionActionFactory->createNew();
        $promotionAction->setType($options['type']);
        $promotionAction->setConfiguration($options['configuration']);

        return $promotionAction;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', PercentageDiscountPromotionActionCommand::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [
                'percentage' => $this->faker->randomNumber(2),
            ])
            ->setNormalizer('configuration', function (Options $options, $configuration) {
                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] *= 100;
                    }

                    if (isset($channelConfiguration['percentage'])) {
                        $configuration[$channelCode]['percentage'] /= 100;
                    }
                }

                if (isset($configuration['percentage'])) {
                    $configuration['percentage'] /= 100;
                }

                return $configuration;
            })
        ;
    }
}
