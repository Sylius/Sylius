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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\LocaleDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\LocaleTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\LocaleUpdaterInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<LocaleInterface>
 *
 * @method static LocaleInterface|Proxy createOne(array $attributes = [])
 * @method static LocaleInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static LocaleInterface|Proxy find(object|array|mixed $criteria)
 * @method static LocaleInterface|Proxy findOrCreate(array $attributes)
 * @method static LocaleInterface|Proxy first(string $sortedField = 'id')
 * @method static LocaleInterface|Proxy last(string $sortedField = 'id')
 * @method static LocaleInterface|Proxy random(array $attributes = [])
 * @method static LocaleInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static LocaleInterface[]|Proxy[] all()
 * @method static LocaleInterface[]|Proxy[] findBy(array $attributes)
 * @method static LocaleInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static LocaleInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method LocaleInterface|Proxy create(array|callable $attributes = [])
 */
class LocaleFactory extends ModelFactory implements LocaleFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $localeFactory,
        private string $baseLocaleCode,
        private LocaleDefaultValuesInterface $defaultValues,
        private LocaleTransformerInterface $transformer,
        private LocaleUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withDefaultCode(): self
    {
        return $this->addState(['code' => $this->baseLocaleCode]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(LocaleInterface $locale, array $attributes): void
    {
        $this->updater->update($locale, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): LocaleInterface {
                /** @var LocaleInterface $locale */
                $locale = $this->localeFactory->createNew();

                $this->update($locale, $attributes);

                return $locale;
            })
            ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Locale::class;
    }
}
