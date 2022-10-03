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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\CustomerDefaultValuesInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CustomerInterface>
 *
 * @method static CustomerInterface|Proxy createOne(array $attributes = [])
 * @method static CustomerInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CustomerInterface|Proxy find(object|array|mixed $criteria)
 * @method static CustomerInterface|Proxy findOrCreate(array $attributes)
 * @method static CustomerInterface|Proxy first(string $sortedField = 'id')
 * @method static CustomerInterface|Proxy last(string $sortedField = 'id')
 * @method static CustomerInterface|Proxy random(array $attributes = [])
 * @method static CustomerInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CustomerInterface[]|Proxy[] all()
 * @method static CustomerInterface[]|Proxy[] findBy(array $attributes)
 * @method static CustomerInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CustomerInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CustomerInterface|Proxy create(array|callable $attributes = [])
 */
final class CustomerFactory extends ModelFactory implements CustomerFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $customerFactory,
        private CustomerGroupFactoryInterface $customerGroupFactory,
        private CustomerDefaultValuesInterface $defaultValues,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
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

    public function withGroup(Proxy|CustomerGroupInterface|string $customerGroup): self
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
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): CustomerInterface {
                /** @var CustomerInterface $customer */
                $customer = $this->customerFactory->createNew();
                $customer->setEmail($attributes['email']);
                $customer->setFirstName($attributes['first_name']);
                $customer->setLastName($attributes['last_name']);
                $customer->setGroup($attributes['customer_group']);
                $customer->setGender($attributes['gender']);
                $customer->setPhoneNumber($attributes['phone_number']);
                $customer->setBirthday($attributes['birthday']);

                return $customer;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Customer::class;
    }
}
