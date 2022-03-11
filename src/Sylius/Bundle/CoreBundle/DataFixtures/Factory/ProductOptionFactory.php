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
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductOptionInterface>
 *
 * @method static ProductOptionInterface|Proxy createOne(array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductOptionInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductOptionInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductOptionInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductOptionInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductOptionInterface|Proxy random(array $attributes = [])
 * @method static ProductOptionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] all()
 * @method static ProductOptionInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductOptionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductOptionInterface|Proxy create(array|callable $attributes = [])
 */
class ProductOptionFactory extends ModelFactory implements ProductOptionFactoryInterface
{
    public function __construct(
        private FactoryInterface $productOptionFactory,
        private FactoryInterface $productOptionValueFactory,
        private RepositoryInterface $localeRepository,
    ) {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function withName(string $name): self
    {
        return $this->addState(['name' => $name]);
    }

    public function withValues(array $values): self
    {
        return $this->addState(['values' => $values]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => null,
            'name' => self::faker()->words(3, true),
            'values' => null,
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                if (is_array($attributes['values'])) {
                    return $attributes;
                }

                $attributes['values'] = [];
                for ($i = 1; $i <= 5; ++$i) {
                    $attributes['values'][sprintf('%s-option#%d', $attributes['code'], $i)] = sprintf('%s #i%d', $attributes['name'], $i);
                }

                return $attributes;
            })
            ->instantiateWith(function(array $attributes): ProductOptionInterface {
                $code = $attributes['code'] ?? StringInflector::nameToCode($attributes['name']);

                /** @var ProductOptionInterface $productOption */
                $productOption = $this->productOptionFactory->createNew();

                $productOption->setCode($code);

                foreach ($this->getLocales() as $localeCode) {
                    $productOption->setCurrentLocale($localeCode);
                    $productOption->setFallbackLocale($localeCode);

                    $productOption->setName($attributes['name']);
                }

                foreach ($attributes['values'] as $code => $value) {
                    /** @var ProductOptionValueInterface $productOptionValue */
                    $productOptionValue = $this->productOptionValueFactory->createNew();
                    $productOptionValue->setCode($code);

                    foreach ($this->getLocales() as $localeCode) {
                        $productOptionValue->setCurrentLocale($localeCode);
                        $productOptionValue->setFallbackLocale($localeCode);

                        $productOptionValue->setValue($value);
                    }

                    $productOption->addValue($productOptionValue);
                }

                return $productOption;
            })
        ;
    }

    protected static function getClass(): string
    {
        return ProductOption::class;
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
