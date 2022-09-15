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

use Sylius\Component\Core\Model\TaxonInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxonInterface>
 *
 * @method static TaxonInterface|Proxy createOne(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxonInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxonInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxonInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy random(array $attributes = [])
 * @method static TaxonInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] all()
 * @method static TaxonInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxonInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxonInterface|Proxy create(array|callable $attributes = [])
 */
interface TaxonFactoryInterface extends WithCodeInterface
{
    public function withName(string $name): self;

    public function withSlug(string $slug): self;

    public function withDescription(string $description): self;

    public function withTranslations(array $translations): self;

    public function withChildren(array $children): self;
}
