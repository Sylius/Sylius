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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Locale>
 *
 * @method static Locale|Proxy createOne(array $attributes = [])
 * @method static Locale[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Locale|Proxy find(object|array|mixed $criteria)
 * @method static Locale|Proxy findOrCreate(array $attributes)
 * @method static Locale|Proxy first(string $sortedField = 'id')
 * @method static Locale|Proxy last(string $sortedField = 'id')
 * @method static Locale|Proxy random(array $attributes = [])
 * @method static Locale|Proxy randomOrCreate(array $attributes = [])
 * @method static Locale[]|Proxy[] all()
 * @method static Locale[]|Proxy[] findBy(array $attributes)
 * @method static Locale[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Locale[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method Locale|Proxy create(array|callable $attributes = [])
 */
final class LocaleFactory extends ModelFactory implements LocaleFactoryInterface
{
    public function __construct(
        private FactoryInterface $localeFactory,
        private RepositoryInterface $localeRepository,
        private string $baseLocaleCode,
    ) {
        parent::__construct();
    }

    public function withDefaultLocaleCode(): self
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
                $code = $attributes['code'];

                if (null !== $locale = $this->localeRepository->findOneBy(['code' => $code])) {
                    return $locale;
                }

                /** @var LocaleInterface $locale */
                $locale = $this->localeFactory->createNew();

                $locale->setCode($code);

                return $locale;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Locale::class;
    }
}
