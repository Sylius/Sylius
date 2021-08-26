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
use Sylius\Component\Promotion\Factory\CatalogPromotionRuleFactoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CatalogPromotionDataTransformer implements DataTransformerInterface
{
    private IriConverterInterface $iriConverter;
    private CatalogPromotionRuleFactoryInterface $catalogPromotionRuleFactory;
    private FactoryInterface $catalogPromotionFactory;

    public function __construct(
        IriConverterInterface $iriConverter,
        CatalogPromotionRuleFactoryInterface $catalogPromotionRuleFactory,
        FactoryInterface $catalogPromotionFactory
    ) {
        $this->iriConverter = $iriConverter;
        $this->catalogPromotionRuleFactory = $catalogPromotionRuleFactory;
        $this->catalogPromotionFactory = $catalogPromotionFactory;
    }

    public function transform($object, string $to, array $context = [])
    {
        $catalogPromotion = $this->catalogPromotionFactory->createNew();
        $catalogPromotion->setCode($object->getCode());
        $catalogPromotion->setName($object->getName());

        foreach($object->rules as $ruleData) {

            $configuration = $this->replaceIriToCodeInConfiguration($ruleData['configuration']);
            $CatalogPromotionRule = $this->catalogPromotionRuleFactory->createWithData(
                $ruleData['type'],
                $catalogPromotion,
                $configuration
            );

            $catalogPromotion->addRule($CatalogPromotionRule);
        }

        return $catalogPromotion;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CatalogPromotion::class === $to;
    }

    private function replaceIriToCodeInConfiguration(array $configuration): array
    {
        $newConfiguration = [];
        foreach ($configuration as $item) {
            array_push($newConfiguration, $this->iriConverter->getItemFromIri($item)->getCode());
        }

        return $newConfiguration;
    }
}
