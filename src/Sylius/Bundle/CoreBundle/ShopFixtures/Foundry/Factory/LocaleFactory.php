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
use Sylius\Component\Locale\Model\Locale;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Locale>
 *
 * @method        Locale|Proxy create(array|callable $attributes = [])
 * @method static Locale|Proxy createOne(array $attributes = [])
 * @method static Locale|Proxy find(object|array|mixed $criteria)
 * @method static Locale|Proxy findOrCreate(array $attributes)
 * @method static Locale|Proxy first(string $sortedField = 'id')
 * @method static Locale|Proxy last(string $sortedField = 'id')
 * @method static Locale|Proxy random(array $attributes = [])
 * @method static Locale|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Locale[]|Proxy[] all()
 * @method static Locale[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Locale[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Locale[]|Proxy[] findBy(array $attributes)
 * @method static Locale[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Locale[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Locale> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Locale> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Locale> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Locale> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Locale> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Locale> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Locale> random(array $attributes = [])
 * @phpstan-method static Proxy<Locale> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Locale> repository()
 * @phpstan-method static list<Proxy<Locale>> all()
 * @phpstan-method static list<Proxy<Locale>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Locale>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Locale>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Locale>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Locale>> randomSet(int $number, array $attributes = [])
 */
final class LocaleFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
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
            'code' => self::faker()->locale(),
            'createdAt' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Locale::class;
    }
}
