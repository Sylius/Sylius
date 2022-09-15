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

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAssociationType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAssociationTypeInterface>
 *
 * @method static ProductAssociationTypeInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAssociationTypeInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAssociationTypeInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAssociationTypeInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAssociationTypeInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAssociationTypeInterface|Proxy random(array $attributes = [])
 * @method static ProductAssociationTypeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] all()
 * @method static ProductAssociationTypeInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAssociationTypeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAssociationTypeInterface|Proxy create(array|callable $attributes = [])
 */
class ProductAssociationTypeFactory extends ModelFactory implements ProductAssociationTypeFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $productAssociationTypeFactory,
        private RepositoryInterface $localeRepository,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ProductAssociationTypeInterface {
                $code = $attributes['code'] ?? StringInflector::nameToCode($attributes['name']);

                /** @var ProductAssociationTypeInterface $productAssociationType */
                $productAssociationType = $this->productAssociationTypeFactory->createNew();

                $productAssociationType->setCode($code);

                foreach ($this->getLocales() as $localeCode) {
                    $productAssociationType->setCurrentLocale($localeCode);
                    $productAssociationType->setFallbackLocale($localeCode);

                    $productAssociationType->setName($attributes['name']);
                }

                return $productAssociationType;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ProductAssociationType::class;
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
