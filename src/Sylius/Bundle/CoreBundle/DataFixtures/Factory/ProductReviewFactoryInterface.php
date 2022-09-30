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

use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ReviewInterface>
 *
 * @method static ReviewInterface|Proxy createOne(array $attributes = [])
 * @method static ReviewInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ReviewInterface|Proxy find(object|array|mixed $criteria)
 * @method static ReviewInterface|Proxy findOrCreate(array $attributes)
 * @method static ReviewInterface|Proxy first(string $sortedField = 'id')
 * @method static ReviewInterface|Proxy last(string $sortedField = 'id')
 * @method static ReviewInterface|Proxy random(array $attributes = [])
 * @method static ReviewInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ReviewInterface[]|Proxy[] all()
 * @method static ReviewInterface[]|Proxy[] findBy(array $attributes)
 * @method static ReviewInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ReviewInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ReviewInterface|Proxy create(array|callable $attributes = [])
 */
interface ProductReviewFactoryInterface extends WithTitleInterface, WithCommentInterface, WithProductInterface, WithStatusInterface
{
    public function withRating(int $rating): self;

    public function withAuthor(Proxy|ReviewerInterface|string $author): self;
}
