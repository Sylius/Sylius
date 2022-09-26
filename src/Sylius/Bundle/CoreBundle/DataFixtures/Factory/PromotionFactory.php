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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\PromotionFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\PromotionFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\PromotionFactoryUpdaterInterface;
use Sylius\Component\Core\Model\Promotion;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<PromotionInterface>
 *
 * @method static PromotionInterface|Proxy createOne(array $attributes = [])
 * @method static PromotionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PromotionInterface|Proxy find(object|array|mixed $criteria)
 * @method static PromotionInterface|Proxy findOrCreate(array $attributes)
 * @method static PromotionInterface|Proxy first(string $sortedField = 'id')
 * @method static PromotionInterface|Proxy last(string $sortedField = 'id')
 * @method static PromotionInterface|Proxy random(array $attributes = [])
 * @method static PromotionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static PromotionInterface[]|Proxy[] all()
 * @method static PromotionInterface[]|Proxy[] findBy(array $attributes)
 * @method static PromotionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PromotionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method PromotionInterface|Proxy create(array|callable $attributes = [])
 */
class PromotionFactory extends ModelFactory implements PromotionFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;
    use WithPriorityTrait;
    use WithChannelsTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $addressFactory,
        private PromotionFactoryDefaultValuesInterface $factoryDefaultValues,
        private PromotionFactoryTransformerInterface $factoryTransformer,
        private PromotionFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withUsageLimit(int $usageLimit): self
    {
        return $this->addState(['usage_limit' => $usageLimit]);
    }

    public function couponBased(): self
    {
        return $this->addState(['coupon_based' => true]);
    }

    public function notCouponBased(): self
    {
        return $this->addState(['coupon_based' => false]);
    }

    public function exclusive(): self
    {
        return $this->addState(['exclusive' => true]);
    }

    public function notExclusive(): self
    {
        return $this->addState(['exclusive' => false]);
    }

    public function withStartDate(\DateTimeInterface|string $startAt): self
    {
        return $this->addState(['starts_at' => $startAt]);
    }

    public function withEndDate(\DateTimeInterface|string $endAt): self
    {
        return $this->addState(['ends_at' => $endAt]);
    }

    public function withRules(array $rules): self
    {
        return $this->addState(['rules' => $rules]);
    }

    public function withActions(array $actions): self
    {
        return $this->addState(['actions' => $actions]);
    }

    public function withCoupons(array $coupons): self
    {
        return $this->addState(['coupons' => $coupons]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(PromotionInterface $promotion, $attributes): void
    {
        $this->factoryUpdater->update($promotion, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(): PromotionInterface {
                /** @var PromotionInterface $promotion */
                $promotion = $this->addressFactory->createNew();

                return $promotion;
            })
            ->afterInstantiate(function(PromotionInterface $promotion, array $attributes): void {
                $this->update($promotion, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Promotion::class;
    }
}
