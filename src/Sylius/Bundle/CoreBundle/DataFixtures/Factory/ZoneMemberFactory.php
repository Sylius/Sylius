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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ZoneMemberInterface>
 *
 * @method static ZoneMemberInterface|Proxy createOne(array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ZoneMemberInterface|Proxy find(object|array|mixed $criteria)
 * @method static ZoneMemberInterface|Proxy findOrCreate(array $attributes)
 * @method static ZoneMemberInterface|Proxy first(string $sortedField = 'id')
 * @method static ZoneMemberInterface|Proxy last(string $sortedField = 'id')
 * @method static ZoneMemberInterface|Proxy random(array $attributes = [])
 * @method static ZoneMemberInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] all()
 * @method static ZoneMemberInterface[]|Proxy[] findBy(array $attributes)
 * @method static ZoneMemberInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ZoneMemberInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ZoneMemberInterface|Proxy create(array|callable $attributes = [])
 */
final class ZoneMemberFactory extends ModelFactory implements ZoneMemberFactoryInterface
{
    public function __construct(private FactoryInterface $zoneMemberFactory, private ZoneFactoryInterface $zoneFactory)
    {
        parent::__construct();
    }

    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }

    public function belongsTo(Proxy|ZoneInterface|string $zone): self
    {
        return $this->addState(function() use ($zone): array {
            if (is_string($zone)) {
                return ['belongs_to' => $this->zoneFactory::randomOrCreate(['code' => $zone])];
            }

            return ['belongs_to' => $zone];
        });
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->unique()->word(),
            'belongs_to' => null,
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ZoneMemberInterface {
                /** @var ZoneMemberInterface $zoneMember */
                $zoneMember = $this->zoneMemberFactory->createNew();

                $zoneMember->setCode($attributes['code']);
                $zoneMember->setBelongsTo($attributes['belongs_to']);

                return $zoneMember;
            })
        ;
    }

    protected static function getClass(): string
    {
        return ZoneMember::class;
    }
}
