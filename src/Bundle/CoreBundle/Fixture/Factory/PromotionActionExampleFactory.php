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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionActionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(private PromotionActionFactoryInterface $promotionActionFactory)
    {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): PromotionActionInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PromotionActionInterface $promotionAction */
        $promotionAction = $this->promotionActionFactory->createNew();
        $promotionAction->setType($options['type']);
        $promotionAction->setConfiguration($options['configuration']);

        return $promotionAction;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', PercentageDiscountPromotionActionCommand::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [
                'percentage' => $this->faker->randomNumber(2),
            ])
            ->setNormalizer('configuration', function (Options $options, array $configuration): array {
                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] = (int) ($configuration[$channelCode]['amount'] * 100);
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
