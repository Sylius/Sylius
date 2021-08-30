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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;

/** @experimental */
final class CatalogPromotionDataTransformer implements DataTransformerInterface
{
    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    public function transform($object, string $to, array $context = [])
    {
        /** @var CatalogPromotionRuleInterface $rule */
        foreach ($object->getRules() as $rule) {
            $rule->setConfiguration($this->replaceIriToCodeInConfiguration($rule->getConfiguration()));
        }

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CatalogPromotion::class === $to;
    }

    private function replaceIriToCodeInConfiguration(array $configuration): array
    {
        $processedConfiguration = [];
        foreach ($configuration as $item) {
            $product = $this->iriConverter->getItemFromIri($item);

            if (!$product instanceof ProductVariantInterface) {
                throw new ItemNotFoundException();
            }

            $processedConfiguration[] = $product->getCode();
        }

        return $processedConfiguration;
    }
}
