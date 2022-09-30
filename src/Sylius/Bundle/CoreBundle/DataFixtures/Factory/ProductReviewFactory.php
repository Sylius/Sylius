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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ProductReviewDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ProductReviewTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ProductReviewUpdaterInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
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
class ProductReviewFactory extends ModelFactory implements ProductReviewFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithTitleTrait;
    use WithCommentTrait;
    use WithProductTrait;
    use WithStatusTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private ReviewFactoryInterface $ProductReviewFactory,
        private ProductReviewDefaultValuesInterface $defaultValues,
        private ProductReviewTransformerInterface $transformer,
        private ProductReviewUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withRating(int $rating): self
    {
        return $this->addState(['rating' => $rating]);
    }

    public function withAuthor(Proxy|ReviewerInterface|string $author): self
    {
        return $this->addState(['author' => $author]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(ReviewInterface $productReview, array $attributes): void
    {
        $this->updater->update($productReview, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ReviewInterface {
                $productReview = $this->ProductReviewFactory->createForSubjectWithReviewer(
                    $attributes['product'],
                    $attributes['author']
                );

                $this->update($productReview, $attributes);

                return $productReview;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ProductReview::class;
    }
}
