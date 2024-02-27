<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DoctrineCollectionValuesNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_doctrine_collection_with_normalization_context_key(
        OrderInterface $order,
        Collection $collection,
    ): void {
        $this->supportsNormalization($order)->shouldReturn(false);
        $this->supportsNormalization($order, null, ['collection_values' => false])->shouldReturn(false);
        $this->supportsNormalization($order, null, ['collection_values' => true])->shouldReturn(false);

        $this->supportsNormalization($collection)->shouldReturn(false);
        $this->supportsNormalization($collection, null, ['collection_values' => false])->shouldReturn(false);
        $this->supportsNormalization($collection, null, ['collection_values' => true])->shouldReturn(true);
    }

    function it_normalizes_collection_values(
        NormalizerInterface $normalizer,
    ): void {
        $this->setNormalizer($normalizer);

        $collection = new ArrayCollection(['1' => ['id' => 1], '2' => ['id' => 2]]);

        $this->normalize($collection, null, ['collection_values' => true]);

        $normalizer->normalize([['id' => 1], ['id' => 2]], null, ['collection_values' => true])->shouldHaveBeenCalled();
    }
}
