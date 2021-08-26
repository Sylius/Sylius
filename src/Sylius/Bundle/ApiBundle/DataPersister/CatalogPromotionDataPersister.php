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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;

/** @experimental  */
final class CatalogPromotionDataPersister implements ContextAwareDataPersisterInterface
{
    private ContextAwareDataPersisterInterface $decoratedDataPersister;
    private IriConverterInterface $iriConverter;

    public function __construct(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        IriConverterInterface $iriConverter
    ) {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->iriConverter = $iriConverter;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CatalogPromotionInterface;
    }

    public function persist($data, array $context = [])
    {
        /** @var CatalogPromotionRuleInterface $rule */
        foreach ($data->getRules() as $rule) {
            $rule->setConfiguration($this->replaceIriToCodeInConfiguration($rule->getConfiguration()));
        }

        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
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
