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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\ShippingMethodFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\ShippingMethodFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\ShippingMethodFactoryUpdaterInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ShippingMethodInterface>
 *
 * @method static ShippingMethodInterface|Proxy createOne(array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShippingMethodInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShippingMethodInterface|Proxy findOrCreate(array $attributes)
 * @method static ShippingMethodInterface|Proxy first(string $sortedField = 'id')
 * @method static ShippingMethodInterface|Proxy last(string $sortedField = 'id')
 * @method static ShippingMethodInterface|Proxy random(array $attributes = [])
 * @method static ShippingMethodInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] all()
 * @method static ShippingMethodInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShippingMethodInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ShippingMethodInterface|Proxy create(array|callable $attributes = [])
 */
class ShippingMethodFactory extends ModelFactory implements ShippingMethodFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;
    use WithZoneTrait;
    use WithTaxCategoryTrait;
    use WithChannelsTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $catalogPromotionFactory,
        private ShippingMethodFactoryDefaultValuesInterface $factoryDefaultValues,
        private ShippingMethodFactoryTransformerInterface $factoryTransformer,
        private ShippingMethodFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withCategory(Proxy|ShippingCategoryInterface|string $category): self
    {
        return $this->addState(['category' => $category]);
    }

    public function withArchiveDate(\DateTimeInterface $archivedAt): self
    {
        return $this->addState(['archived_at' => $archivedAt]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(ShippingMethodInterface $shippingMethod, array $attributes): void
    {
        $this->factoryUpdater->update($shippingMethod, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ShippingMethodInterface {
                /** @var ShippingMethodInterface $shippingMethod */
                $shippingMethod = $this->catalogPromotionFactory->createNew();

                $this->update($shippingMethod, $attributes);

                return $shippingMethod;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ShippingMethod::class;
    }
}
