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

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CatalogPromotionInterface>
 *
 * @method static CatalogPromotionInterface|Proxy createOne(array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CatalogPromotionInterface|Proxy find(object|array|mixed $criteria)
 * @method static CatalogPromotionInterface|Proxy findOrCreate(array $attributes)
 * @method static CatalogPromotionInterface|Proxy first(string $sortedField = 'id')
 * @method static CatalogPromotionInterface|Proxy last(string $sortedField = 'id')
 * @method static CatalogPromotionInterface|Proxy random(array $attributes = [])
 * @method static CatalogPromotionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] all()
 * @method static CatalogPromotionInterface[]|Proxy[] findBy(array $attributes)
 * @method static CatalogPromotionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CatalogPromotionInterface|Proxy create(array|callable $attributes = [])
 */
interface CatalogPromotionFactoryInterface extends WithCodeInterface, WithNameInterface, WithDescriptionInterface, ToggableInterface, WithChannelsInterface
{
    public function withLabel(string $label): self;

    public function withScopes(array $scopes): self;

    public function withActions(array $actions): self;

    public function withPriority(int $priority): self;

    public function exclusive(): self;

    public function notExclusive(): self;

    public function withStartDate(\DateTimeInterface|string $startDate): self;

    public function withEndDate(\DateTimeInterface|string $endDate): self;
}
