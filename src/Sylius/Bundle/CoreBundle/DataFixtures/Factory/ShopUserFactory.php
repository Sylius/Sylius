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
final class ShopUserFactory extends ModelFactory implements ShopUserFactoryInterface
{
    public function __construct(
        private FactoryInterface $shopUserFactory,
        private FactoryInterface $customerFactory,
        private CustomerGroupFactoryInterface $customerGroupFactory,
    ) {
        parent::__construct();
    }

    public function withEmail(string $email): self
    {
        return $this->addState(['email' => $email]);
    }

    public function withFirstName(string $firstName): self
    {
        return $this->addState(['first_name' => $firstName]);
    }

    public function withLastName(string $lastName): self
    {
        return $this->addState(['last_name' => $lastName]);
    }

    public function male(): self
    {
        return $this->addState(['gender' => CustomerInterface::MALE_GENDER]);
    }

    public function female(): self
    {
        return $this->addState(['gender' => CustomerInterface::FEMALE_GENDER]);
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        return $this->addState(['phone_number' => $phoneNumber]);
    }

    public function withBirthday(\DateTimeInterface|string $birthday): self
    {
        if (is_string($birthday)) {
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
        return $this->addState(function () use ($customerGroup): array {
            if (is_string($customerGroup)) {
                return ['customer_group' => $this->customerGroupFactory::randomOrCreate(['code' => $customerGroup])];
            }

            return ['customer_group' => $customerGroup];
        });
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'first_name' => self::faker()->firstName(),
            'last_name' => self::faker()->lastName(),
            'enabled' => true,
            'password' => 'password123',
            'customer_group' => $this->customerGroupFactory::randomOrCreate(),
            'gender' => CustomerInterface::UNKNOWN_GENDER,
            'phone_number' => self::faker()->phoneNumber(),
            'birthday' => self::faker()->dateTimeThisCentury(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ShopUserInterface {
                /** @var CustomerInterface $customer */
                $customer = $this->customerFactory->createNew();
                $customer->setEmail($attributes['email']);
                $customer->setFirstName($attributes['first_name']);
                $customer->setLastName($attributes['last_name']);
                $customer->setGroup($attributes['customer_group']);
                $customer->setGender($attributes['gender']);
                $customer->setPhoneNumber($attributes['phone_number']);
                $customer->setBirthday($attributes['birthday']);

                /** @var ShopUserInterface $user */
                $user = $this->shopUserFactory->createNew();
                $user->setPlainPassword($attributes['password']);
                $user->setEnabled($attributes['enabled']);
                $user->addRole('ROLE_USER');
                $user->setCustomer($customer);

                return $user;
            })
        ;
    }

    protected static function getClass(): string
    {
        return ShopUser::class;
    }
}
