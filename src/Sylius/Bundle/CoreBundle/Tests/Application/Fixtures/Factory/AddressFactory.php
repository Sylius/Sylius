<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Application\Fixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\FactoryWithModelClassAwareInterface;
use Sylius\Component\Addressing\Model\Address;
use Zenstruck\Foundry\ModelFactory;

class AddressFactory extends ModelFactory implements FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    protected static function getClass(): string
    {
        return self::$modelClass ?? Address::class;
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return [];
    }
}
