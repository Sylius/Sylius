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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
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
class TaxRateFactory extends ModelFactory implements TaxRateFactoryInterface
{
    public function __construct(
        private FactoryInterface $countryFactory,
        private ZoneFactoryInterface $zoneFactory,
        private TaxCategoryFactoryInterface $taxCategoryFactory
    ) {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
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

    public function withZone(Proxy|ZoneInterface|string $zone): self
    {
        return $this->addState(['zone' => $zone]);
    }

    public function withCategory(Proxy|TaxCategoryInterface|string $category): self
    {
        return $this->addState(['category' => $category]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
            'amount' => self::faker()->randomFloat(2, 0, 0.4),
            'included_in_price' => self::faker()->boolean(),
            'calculator' => 'default',
            'zone' => $this->zoneFactory->randomOrCreate(),
            'category' => $this->taxCategoryFactory->randomOrCreate(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                $attributes['code'] = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);

                if (is_string($attributes['zone'])) {
                    $attributes['zone'] = $this->zoneFactory->randomOrCreate(['code' => $attributes['zone']]);
                }

                if (is_string($attributes['category'])) {
                    $attributes['category'] = $this->taxCategoryFactory->randomOrCreate(['code' => $attributes['category']]);
                }

                return $attributes;
            })
            ->instantiateWith(function(array $attributes): TaxRateInterface {
                /** @var TaxRateInterface $taxRate */
                $taxRate = $this->countryFactory->createNew();

                $taxRate->setCode($attributes['code']);
                $taxRate->setName($attributes['name']);
                $taxRate->setAmount($attributes['amount']);
                $taxRate->setIncludedInPrice($attributes['included_in_price']);
                $taxRate->setCalculator($attributes['calculator']);
                $taxRate->setZone($attributes['zone']);
                $taxRate->setCategory($attributes['category']);

                return $taxRate;
            })
        ;
    }

    protected static function getClass(): string
    {
        return TaxRate::class;
    }
}
