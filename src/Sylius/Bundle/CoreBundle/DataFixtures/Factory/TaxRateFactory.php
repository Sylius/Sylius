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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\TaxRateDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\TaxRateTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\TaxRateUpdaterInterface;
use Sylius\Component\Core\Model\TaxRate;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxRateInterface>
 *
 * @method static TaxRateInterface|Proxy createOne(array $attributes = [])
 * @method static TaxRateInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxRateInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxRateInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxRateInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxRateInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxRateInterface|Proxy random(array $attributes = [])
 * @method static TaxRateInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxRateInterface[]|Proxy[] all()
 * @method static TaxRateInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxRateInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxRateInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxRateInterface|Proxy create(array|callable $attributes = [])
 */
class TaxRateFactory extends ModelFactory implements TaxRateFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithZoneTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface              $countryFactory,
        private TaxRateDefaultValuesInterface $factoryDefaultValues,
        private TaxRateTransformerInterface   $factoryTransformer,
        private TaxRateUpdaterInterface       $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withAmount(float $amount): self
    {
        return $this->addState(['amount' => $amount]);
    }

    public function includedInPrice(): self
    {
        return $this->addState(['included_in_price' => true]);
    }

    public function notIncludedInPrice(): self
    {
        return $this->addState(['included_in_price' => false]);
    }

    public function withCalculator(string $calculator): self
    {
        return $this->addState(['calculator' => $calculator]);
    }

    public function withCategory(Proxy|TaxCategoryInterface|string $category): self
    {
        return $this->addState(['category' => $category]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(TaxRateInterface $taxRate, array $attributes): void
    {
        $this->factoryUpdater->update($taxRate, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(): TaxRateInterface {
                /** @var TaxRateInterface $taxRate */
                $taxRate = $this->countryFactory->createNew();

                return $taxRate;
            })
            ->afterInstantiate(function (TaxRateInterface $taxRate, array $attributes): void {
                $this->update($taxRate, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? TaxRate::class;
    }
}
