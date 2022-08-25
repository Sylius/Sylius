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

use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAttributeInterface>
 *
 * @method static ProductAttributeInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAttributeInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAttributeInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAttributeInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy random(array $attributes = [])
 * @method static ProductAttributeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] all()
 * @method static ProductAttributeInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAttributeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAttributeInterface|Proxy create(array|callable $attributes = [])
 */
class ProductAttributeFactory extends ModelFactory implements ProductAttributeFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static string $modelClass;

    public function __construct(
        private AttributeFactoryInterface $productAttributeFactory,
        private RepositoryInterface $localeRepository,
        private array $attributeTypes,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function withType(string $type): self
    {
        return $this->addState(['type' => $type]);
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
    }

    public function translatable(): self
    {
        return $this->addState(['translatable' => true]);
    }

    public function untranslatable(): self
    {
        return $this->addState(['translatable' => false]);
    }

    public function withConfiguration(array $configuration): self
    {
        return $this->addState(['configuration' => $configuration]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
            'type' => self::faker()->randomElement(array_keys($this->attributeTypes)),
            'translatable' => true,
            'configuration' => [],
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ProductAttributeInterface {
                $code = $attributes['code'] ?? StringInflector::nameToCode($attributes['name']);

                /** @var ProductAttributeInterface $productAttribute */
                $productAttribute = $this->productAttributeFactory->createTyped($attributes['type']);

                $productAttribute->setCode($code);
                $productAttribute->setTranslatable($attributes['translatable']);

                foreach ($this->getLocales() as $localeCode) {
                    $productAttribute->setCurrentLocale($localeCode);
                    $productAttribute->setFallbackLocale($localeCode);

                    $productAttribute->setName($attributes['name']);
                }

                $productAttribute->setConfiguration($attributes['configuration']);

                return $productAttribute;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass;
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
