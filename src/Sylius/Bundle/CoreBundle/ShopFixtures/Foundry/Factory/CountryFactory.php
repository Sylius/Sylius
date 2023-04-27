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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\CountryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\CountryTransformerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\CountryUpdaterInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<CountryInterface>
 *
 * @method        CountryInterface|Proxy create(array|callable $attributes = [])
 * @method static CountryInterface|Proxy createOne(array $attributes = [])
 * @method static CountryInterface|Proxy find(object|array|mixed $criteria)
 * @method static CountryInterface|Proxy findOrCreate(array $attributes)
 * @method static CountryInterface|Proxy first(string $sortedField = 'id')
 * @method static CountryInterface|Proxy last(string $sortedField = 'id')
 * @method static CountryInterface|Proxy random(array $attributes = [])
 * @method static CountryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static CountryInterface[]|Proxy[] all()
 * @method static CountryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CountryInterface[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static CountryInterface[]|Proxy[] findBy(array $attributes)
 * @method static CountryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CountryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<CountryInterface> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<CountryInterface> createOne(array $attributes = [])
 * @phpstan-method static Proxy<CountryInterface> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<CountryInterface> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<CountryInterface> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<CountryInterface> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<CountryInterface> random(array $attributes = [])
 * @phpstan-method static Proxy<CountryInterface> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<CountryInterface> repository()
 * @phpstan-method static list<Proxy<CountryInterface>> all()
 * @phpstan-method static list<Proxy<CountryInterface>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<CountryInterface>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<CountryInterface>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<CountryInterface>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<CountryInterface>> randomSet(int $number, array $attributes = [])
 */
final class CountryFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use EnabledTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $factory,
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
        return $this->defaultValues->getDefaultValues(self::faker());
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transformer->transform($attributes);
            })
            ->instantiateWith(function(): CountryInterface {
                /** @var CountryInterface $country */
                $country = $this->factory->createNew();

                return $country;
            })
            ->afterInstantiate(function (CountryInterface $country, array $attributes): void {
                $this->updater->update($country, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Country::class;
    }
}
