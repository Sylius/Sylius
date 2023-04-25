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

use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Bundle\CoreBundle\ShopFixtures\DefaultValues\ShopUserDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\CustomerUpdaterInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\ShopUserUpdaterInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ShopUserInterface>
 *
 * @method        ShopUserInterface|Proxy create(array|callable $attributes = [])
 * @method static ShopUserInterface|Proxy createOne(array $attributes = [])
 * @method static ShopUserInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShopUserInterface|Proxy findOrCreate(array $attributes)
 * @method static ShopUserInterface|Proxy first(string $sortedField = 'id')
 * @method static ShopUserInterface|Proxy last(string $sortedField = 'id')
 * @method static ShopUserInterface|Proxy random(array $attributes = [])
 * @method static ShopUserInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static ShopUserInterface[]|Proxy[] all()
 * @method static ShopUserInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShopUserInterface[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static ShopUserInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShopUserInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ShopUserInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<ShopUserInterface> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<ShopUserInterface> createOne(array $attributes = [])
 * @phpstan-method static Proxy<ShopUserInterface> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<ShopUserInterface> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<ShopUserInterface> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShopUserInterface> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShopUserInterface> random(array $attributes = [])
 * @phpstan-method static Proxy<ShopUserInterface> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<ShopUserInterface> repository()
 * @phpstan-method static list<Proxy<ShopUserInterface>> all()
 * @phpstan-method static list<Proxy<ShopUserInterface>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<ShopUserInterface>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<ShopUserInterface>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<ShopUserInterface>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<ShopUserInterface>> randomSet(int $number, array $attributes = [])
 */
final class ShopUserFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $shopUserFactory,
        private FactoryInterface $customerFactory,
        private ShopUserDefaultValuesInterface $shopUserDefaultValues,
        private ShopUserUpdaterInterface $shopUserUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return $this->shopUserDefaultValues->getDefaultValues(self::faker());
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ShopUserInterface {
                $shopUser = $this->shopUserFactory->createNew();
                $customer = $this->customerFactory->createNew();

                $shopUser->setCustomer($customer);

                return $shopUser;
            })
            ->afterInstantiate(function (ShopUserInterface $shopUser, array $attributes): void {
                $this->shopUserUpdater->update($shopUser, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ShopUser::class;
    }
}
