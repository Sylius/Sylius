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

use Sylius\Bundle\CustomerBundle\Doctrine\ORM\CustomerGroupRepository;
use Sylius\Component\Customer\Model\CustomerGroup;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<CustomerGroup>
 *
 * @method        CustomerGroup|Proxy create(array|callable $attributes = [])
 * @method static CustomerGroup|Proxy createOne(array $attributes = [])
 * @method static CustomerGroup|Proxy find(object|array|mixed $criteria)
 * @method static CustomerGroup|Proxy findOrCreate(array $attributes)
 * @method static CustomerGroup|Proxy first(string $sortedField = 'id')
 * @method static CustomerGroup|Proxy last(string $sortedField = 'id')
 * @method static CustomerGroup|Proxy random(array $attributes = [])
 * @method static CustomerGroup|Proxy randomOrCreate(array $attributes = [])
 * @method static CustomerGroupRepository|RepositoryProxy repository()
 * @method static CustomerGroup[]|Proxy[] all()
 * @method static CustomerGroup[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CustomerGroup[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static CustomerGroup[]|Proxy[] findBy(array $attributes)
 * @method static CustomerGroup[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CustomerGroup[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<CustomerGroup> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<CustomerGroup> createOne(array $attributes = [])
 * @phpstan-method static Proxy<CustomerGroup> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<CustomerGroup> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<CustomerGroup> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<CustomerGroup> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<CustomerGroup> random(array $attributes = [])
 * @phpstan-method static Proxy<CustomerGroup> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<CustomerGroup> repository()
 * @phpstan-method static list<Proxy<CustomerGroup>> all()
 * @phpstan-method static list<Proxy<CustomerGroup>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<CustomerGroup>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<CustomerGroup>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<CustomerGroup>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<CustomerGroup>> randomSet(int $number, array $attributes = [])
 */
final class CustomerGroupFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->text(),
            'name' => self::faker()->text(),
        ];
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? CustomerGroup::class;
    }
}
