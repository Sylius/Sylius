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
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    /**
     * @param RepositoryInterface<LocaleInterface>|null $localeRepository
     */
    public function __construct(
        private FactoryInterface $promotionFactory,
        private ExampleFactoryInterface $promotionRuleExampleFactory,
        private ExampleFactoryInterface $promotionActionExampleFactory,
        private ChannelRepositoryInterface $channelRepository,
        private ?FactoryInterface $couponFactory = null,
        private ?RepositoryInterface $localeRepository = null,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);

        if ($this->couponFactory === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.8',
                'Not passing a $couponFactory to %s constructor is deprecated and will be removed in Sylius 2.0.',
                self::class,
            );
        }

        if ($this->localeRepository === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                'Not passing a $localeRepository to %s constructor is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function create(array $options = []): PromotionInterface
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
        $promotion->setPriority((int) $options['priority']);
        $promotion->setAppliesToDiscounted($options['applies_to_discounted']);

        if (isset($options['starts_at'])) {
            $promotion->setStartsAt(new \DateTime($options['starts_at']));
        }

        if (isset($options['ends_at'])) {
            $promotion->setEndsAt(new \DateTime($options['ends_at']));
        }

        if (isset($options['archived_at'])) {
            $promotion->setArchivedAt(new \DateTime($options['archived_at']));
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

        foreach ($options['coupons'] as $coupon) {
            $promotion->addCoupon($coupon);
        }

        foreach ($this->getLocales() as $localeCode) {
            $promotion->setCurrentLocale($localeCode);
            $promotion->setFallbackLocale($localeCode);

            $promotion->setLabel($options['label']);
        }

        return $promotion;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('name', $this->faker->words(3, true))
            ->setDefault('label', fn (Options $options): string => $options['name'])
            ->setDefault('description', $this->faker->sentence())
            ->setDefault('usage_limit', null)
            ->setDefault('coupon_based', false)
            ->setDefault('exclusive', $this->faker->boolean(25))
            ->setDefault('priority', 0)
            ->setDefault('applies_to_discounted', true)
            ->setDefault('starts_at', null)
            ->setAllowedTypes('starts_at', ['null', 'string'])
            ->setDefault('ends_at', null)
            ->setAllowedTypes('ends_at', ['null', 'string'])
            ->setDefault('archived_at', null)
            ->setAllowedTypes('archived_at', ['null', 'string'])
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefined('rules')
            ->setNormalizer('rules', function (Options $options, ?array $rules): array {
                if (null === $rules) {
                    return [];
                }

                return empty($rules) ? [[]] : $rules;
            })
            ->setDefined('actions')
            ->setNormalizer('actions', function (Options $options, ?array $actions): array {
                if (null === $actions) {
                    return [];
                }

                return empty($actions) ? [[]] : $actions;
            })

            ->setDefault('coupons', [])
            ->setAllowedTypes('coupons', 'array')
            ->setNormalizer('coupons', self::getCouponNormalizer($this->couponFactory))
        ;
    }

    private static function getCouponNormalizer(?FactoryInterface $couponFactory): \Closure
    {
        return function (Options $options, array $couponDefinitions) use ($couponFactory): array {
            if (null === $couponFactory) {
                throw new \RuntimeException('You must configure a $couponFactory');
            }

            if (!$options['coupon_based'] && count($couponDefinitions) > 0) {
                throw new InvalidArgumentException('Cannot define coupons for promotion that is not coupon based');
            }

            $coupons = [];
            foreach ($couponDefinitions as $couponDefinition) {
                /** @var PromotionCouponInterface $coupon */
                $coupon = $couponFactory->createNew();
                $coupon->setCode($couponDefinition['code']);
                $coupon->setPerCustomerUsageLimit($couponDefinition['per_customer_usage_limit'] ?? null);
                $coupon->setReusableFromCancelledOrders($couponDefinition['reusable_from_cancelled_orders'] ?? true);
                $coupon->setUsageLimit($couponDefinition['usage_limit']);

                if (null !== ($couponDefinition['expires_at'] ?? null)) {
                    $coupon->setExpiresAt(new \DateTime($couponDefinition['expires_at']));
                }

                $coupons[] = $coupon;
            }

            return $coupons;
        };
    }

    /** @return iterable<string|null> */
    private function getLocales(): iterable
    {
        if (null === $this->localeRepository) {
            return [];
        }

        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
