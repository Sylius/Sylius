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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\Country;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Country>
 *
 * @method        Country|Proxy create(array|callable $attributes = [])
 * @method static Country|Proxy createOne(array $attributes = [])
 * @method static Country|Proxy find(object|array|mixed $criteria)
 * @method static Country|Proxy findOrCreate(array $attributes)
 * @method static Country|Proxy first(string $sortedField = 'id')
 * @method static Country|Proxy last(string $sortedField = 'id')
 * @method static Country|Proxy random(array $attributes = [])
 * @method static Country|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Country[]|Proxy[] all()
 * @method static Country[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Country[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Country[]|Proxy[] findBy(array $attributes)
 * @method static Country[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Country[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Country> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Country> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Country> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Country> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Country> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Country> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Country> random(array $attributes = [])
 * @phpstan-method static Proxy<Country> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Country> repository()
 * @phpstan-method static list<Proxy<Country>> all()
 * @phpstan-method static list<Proxy<Country>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Country>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Country>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Country>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Country>> randomSet(int $number, array $attributes = [])
 */
final class CountryFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->countryCode,
            'enabled' => self::faker()->boolean(),
        ];
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Country::class;
    }
}
