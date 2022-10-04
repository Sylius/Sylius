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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\CountryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\ToggableTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\CountryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\CountryUpdaterInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CountryInterface>
 *
 * @method static CountryInterface|Proxy createOne(array $attributes = [])
 * @method static CountryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CountryInterface|Proxy find(object|array|mixed $criteria)
 * @method static CountryInterface|Proxy findOrCreate(array $attributes)
 * @method static CountryInterface|Proxy first(string $sortedField = 'id')
 * @method static CountryInterface|Proxy last(string $sortedField = 'id')
 * @method static CountryInterface|Proxy random(array $attributes = [])
 * @method static CountryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CountryInterface[]|Proxy[] all()
 * @method static CountryInterface[]|Proxy[] findBy(array $attributes)
 * @method static CountryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CountryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CountryInterface|Proxy create(array|callable $attributes = [])
 */
class CountryFactory extends ModelFactory implements CountryFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use ToggableTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $countryFactory,
        private CountryDefaultValuesInterface $defaultValues,
        private CountryTransformerInterface $transformer,
        private CountryUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(CountryInterface $country, array $attributes): void
    {
        $this->updater->update($country, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function (array $attributes): CountryInterface {
                /** @var Country $country */
                $country = $this->countryFactory->createNew();

                $this->update($country, $attributes);

                return $country;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Country::class;
    }
}
