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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ShopUserDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ShopUserTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ShopUserUpdaterInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ShopUserInterface>
 *
 * @method static ShopUserInterface|Proxy createOne(array $attributes = [])
 * @method static ShopUserInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShopUserInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShopUserInterface|Proxy findOrCreate(array $attributes)
 * @method static ShopUserInterface|Proxy first(string $sortedField = 'id')
 * @method static ShopUserInterface|Proxy last(string $sortedField = 'id')
 * @method static ShopUserInterface|Proxy random(array $attributes = [])
 * @method static ShopUserInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ShopUserInterface[]|Proxy[] all()
 * @method static ShopUserInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShopUserInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ShopUserInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ShopUserInterface|Proxy create(array|callable $attributes = [])
 */
final class ShopUserFactory extends ModelFactory implements ShopUserFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithEmailTrait;
    use FemaleTrait;
    use MaleTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $shopUserFactory,
        private FactoryInterface $customerFactory,
        private ShopUserDefaultValuesInterface $defaultValues,
        private ShopUserTransformerInterface $transformer,
        private ShopUserUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withFirstName(string $firstName): self
    {
        return $this->addState(['first_name' => $firstName]);
    }

    public function withLastName(string $lastName): self
    {
        return $this->addState(['last_name' => $lastName]);
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        return $this->addState(['phone_number' => $phoneNumber]);
    }

    public function withBirthday(\DateTimeInterface|string $birthday): self
    {
        if (\is_string($birthday)) {
            $birthday = new \DateTimeImmutable($birthday);
        }

        return $this->addState(['birthday' => $birthday]);
    }

    public function withPassword(string $password): self
    {
        return $this->addState(['password' => $password]);
    }

    public function withCustomerGroup(Proxy|CustomerGroupInterface|string $customerGroup): self
    {
        return $this->addState(['customer_group' => $customerGroup]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(ShopUserInterface $shopUser, array $attributes): void
    {
        $this->updater->update($shopUser, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transformer->transform($attributes);
            })
            ->instantiateWith(function (array $attributes): ShopUserInterface {
                /** @var CustomerInterface $customer */
                $customer = $this->customerFactory->createNew();

                /** @var ShopUserInterface $user */
                $user = $this->shopUserFactory->createNew();
                $user->setCustomer($customer);

                $this->update($user, $attributes);

                return $user;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ShopUser::class;
    }
}
