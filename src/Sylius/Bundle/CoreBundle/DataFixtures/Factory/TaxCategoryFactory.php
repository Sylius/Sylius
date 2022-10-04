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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithDescriptionTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameTrait;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategory;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxCategoryInterface>
 *
 * @method static TaxCategoryInterface|Proxy createOne(array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxCategoryInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxCategoryInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxCategoryInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxCategoryInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxCategoryInterface|Proxy random(array $attributes = [])
 * @method static TaxCategoryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] all()
 * @method static TaxCategoryInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxCategoryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxCategoryInterface|Proxy create(array|callable $attributes = [])
 */
class TaxCategoryFactory extends ModelFactory implements TaxCategoryFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;

    private static ?string $modelClass = null;

    public function __construct(private FactoryInterface $taxCategoryFactory)
    {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
            'description' => self::faker()->paragraph(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): TaxCategoryInterface {
                $code = $attributes['code'] ?? StringInflector::nameToCode($attributes['name']);

                /** @var TaxCategoryInterface $taxCategory */
                $taxCategory = $this->taxCategoryFactory->createNew();

                $taxCategory->setCode($code);
                $taxCategory->setName($attributes['name']);
                $taxCategory->setDescription($attributes['description']);

                return $taxCategory;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? TaxCategory::class;
    }
}
