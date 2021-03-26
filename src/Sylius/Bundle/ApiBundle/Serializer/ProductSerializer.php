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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductSerializer implements ContextAwareNormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var int */
    private $reviewsLimit;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        int $reviewsLimit
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->reviewsLimit = $reviewsLimit;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductInterface::class);

        $productReviews = $object->getAcceptedReviews();
        $reviewsCount = count($productReviews);

        if ($reviewsCount > $this->reviewsLimit) {
            for ($i = 0; $i < $reviewsCount - $this->reviewsLimit; $i++) {
                $object->removeReview($productReviews->get($i));
            }
        }

        return $this->objectNormalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        return $data instanceof ProductInterface && $this->isShopGet($context);
    }

    private function isShopGet(array $context): bool
    {
        return isset($context['item_operation_name']) && ($context['item_operation_name'] === 'shop_get');
    }
}
