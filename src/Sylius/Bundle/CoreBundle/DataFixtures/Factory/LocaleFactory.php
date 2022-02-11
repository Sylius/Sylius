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
final class LocaleFactory extends ModelFactory implements LocaleFactoryInterface
{
    public function __construct(
        private FactoryInterface $localeFactory,
        private string $baseLocaleCode,
    ) {
        parent::__construct();
    }

    public function withDefaultCode(): self
    {
        return $this->addState(['code' => $this->baseLocaleCode]);
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->unique()->locale(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): LocaleInterface {
                /** @var LocaleInterface $locale */
                $locale = $this->localeFactory->createNew();

                $locale->setCode($attributes['code']);

                return $locale;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Locale::class;
    }
}
