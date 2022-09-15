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

use Sylius\Component\Core\Model\AdminUserInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<AdminUserInterface>
 *
 * @method static AdminUserInterface|Proxy createOne(array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AdminUserInterface|Proxy find(object|array|mixed $criteria)
 * @method static AdminUserInterface|Proxy findOrCreate(array $attributes)
 * @method static AdminUserInterface|Proxy first(string $sortedField = 'id')
 * @method static AdminUserInterface|Proxy last(string $sortedField = 'id')
 * @method static AdminUserInterface|Proxy random(array $attributes = [])
 * @method static AdminUserInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] all()
 * @method static AdminUserInterface[]|Proxy[] findBy(array $attributes)
 * @method static AdminUserInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method AdminUserInterface|Proxy create(array|callable $attributes = [])
 */
interface AdminUserFactoryInterface extends WithFirstNameInterface
{
    public function withEmail(string $email): self;

    public function withUsername(string $username): self;

    public function enabled(): self;

    public function disabled(): self;

    public function withPassword(string $password): self;

    public function withApiAccess(): self;

    public function withLastName(string $lastName): self;

    public function withAvatar(string $avatar): self;
}
