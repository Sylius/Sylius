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
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<CustomerGroupInterface>
 *
 * @method        CustomerGroupInterface|Proxy create(array|callable $attributes = [])
 * @method static CustomerGroupInterface|Proxy createOne(array $attributes = [])
 * @method static CustomerGroupInterface|Proxy find(object|array|mixed $criteria)
 * @method static CustomerGroupInterface|Proxy findOrCreate(array $attributes)
 * @method static CustomerGroupInterface|Proxy first(string $sortedField = 'id')
 * @method static CustomerGroupInterface|Proxy last(string $sortedField = 'id')
 * @method static CustomerGroupInterface|Proxy random(array $attributes = [])
 * @method static CustomerGroupInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CustomerGroupRepository|RepositoryProxy repository()
 * @method static CustomerGroupInterface[]|Proxy[] all()
 * @method static CustomerGroupInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CustomerGroupInterface[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static CustomerGroupInterface[]|Proxy[] findBy(array $attributes)
 * @method static CustomerGroupInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CustomerGroupInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<CustomerGroupInterface> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<CustomerGroupInterface> createOne(array $attributes = [])
 * @phpstan-method static Proxy<CustomerGroupInterface> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<CustomerGroupInterface> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<CustomerGroupInterface> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<CustomerGroupInterface> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<CustomerGroupInterface> random(array $attributes = [])
 * @phpstan-method static Proxy<CustomerGroupInterface> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<CustomerGroupInterface> repository()
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> all()
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<CustomerGroupInterface>> randomSet(int $number, array $attributes = [])
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
