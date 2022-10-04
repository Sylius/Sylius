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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\AdminUserDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\AdminUserTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\AdminUserUpdaterInterface;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
class AdminUserFactory extends ModelFactory implements AdminUserFactoryInterface, FactoryWithModelClassAwareInterface
{
    use ToggableTrait;
    use WithEmailTrait;
    use WithFirstNameTrait;
    use WithLastNameTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $adminUserFactory,
        private AdminUserDefaultValuesInterface $defaultValues,
        private AdminUserTransformerInterface $transformer,
        private AdminUserUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withUsername(string $username): self
    {
        return $this->addState(['username' => $username]);
    }

    public function withPassword(string $password): self
    {
        return $this->addState(['password' => $password]);
    }

    public function withApiAccess(): self
    {
        return $this->addState(['api' => true]);
    }

    public function withAvatar(string $avatar): self
    {
        return $this->addState(['avatar' => $avatar]);
    }

    public function withLocaleCode(string $localeCode): self
    {
        return $this->addState(['locale_code' => $localeCode]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(AdminUserInterface $adminUser, $attributes): void
    {
        $this->updater->update($adminUser, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(): AdminUserInterface {
                /** @var AdminUserInterface $adminUser */
                $adminUser = $this->adminUserFactory->createNew();

                return $adminUser;
            })
            ->afterInstantiate(function(AdminUserInterface $adminUser, array $attributes): void {
                $this->update($adminUser, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? AdminUser::class;
    }
}
