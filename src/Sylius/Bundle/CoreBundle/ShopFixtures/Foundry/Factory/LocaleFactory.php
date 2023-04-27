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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\LocaleDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\LocaleTransformerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\LocaleUpdaterInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<LocaleInterface>
 *
 * @method        LocaleInterface|Proxy create(array|callable $attributes = [])
 * @method static LocaleInterface|Proxy createOne(array $attributes = [])
 * @method static LocaleInterface|Proxy find(object|array|mixed $criteria)
 * @method static LocaleInterface|Proxy findOrCreate(array $attributes)
 * @method static LocaleInterface|Proxy first(string $sortedField = 'id')
 * @method static LocaleInterface|Proxy last(string $sortedField = 'id')
 * @method static LocaleInterface|Proxy random(array $attributes = [])
 * @method static LocaleInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static LocaleInterface[]|Proxy[] all()
 * @method static LocaleInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static LocaleInterface[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static LocaleInterface[]|Proxy[] findBy(array $attributes)
 * @method static LocaleInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LocaleInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<LocaleInterface> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<LocaleInterface> createOne(array $attributes = [])
 * @phpstan-method static Proxy<LocaleInterface> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<LocaleInterface> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<LocaleInterface> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<LocaleInterface> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<LocaleInterface> random(array $attributes = [])
 * @phpstan-method static Proxy<LocaleInterface> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<LocaleInterface> repository()
 * @phpstan-method static list<Proxy<LocaleInterface>> all()
 * @phpstan-method static list<Proxy<LocaleInterface>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<LocaleInterface>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<LocaleInterface>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<LocaleInterface>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<LocaleInterface>> randomSet(int $number, array $attributes = [])
 */
final class LocaleFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $factory,
        private LocaleDefaultValuesInterface $defaultValues,
        private LocaleTransformerInterface $transformer,
        private LocaleUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->locale(),
            'createdAt' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transformer->transform($attributes);
            })
            ->instantiateWith(function(): LocaleInterface {
                /** @var LocaleInterface $locale */
                $locale = $this->factory->createNew();

                return $locale;
            })
            ->afterInstantiate(function (LocaleInterface $locale, array $attributes): void {
                $this->updater->update($locale, $attributes);
            })
            ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Locale::class;
    }
}
