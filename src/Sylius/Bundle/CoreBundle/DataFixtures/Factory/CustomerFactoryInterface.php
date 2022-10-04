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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\FemaleInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\MaleInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithEmailInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithFirstNameInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithLastNameInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithPhoneNumberInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
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
interface CustomerFactoryInterface extends WithEmailInterface, WithFirstNameInterface, WithLastNameInterface, MaleInterface, FemaleInterface, WithPhoneNumberInterface
{
    public function withBirthday(\DateTimeInterface|string $birthday): self;

    public function withPassword(string $password): self;

    public function withGroup(Proxy|CustomerGroupInterface|string $customerGroup): self;
}
