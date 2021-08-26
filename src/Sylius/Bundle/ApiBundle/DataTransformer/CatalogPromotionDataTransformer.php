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
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionRule;

final class CatalogPromotionDataTransformer implements DataTransformerInterface
{
    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    public function transform($object, string $to, array $context = [])
    {
        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setCode($object->getCode());
        $catalogPromotion->setName($object->getName());

        foreach($object->rules as $ruleData) {

            $CatalogPromotionRule = new CatalogPromotionRule();
            $CatalogPromotionRule->setCatalogPromotion($catalogPromotion);
            $CatalogPromotionRule->setType($ruleData['type']);

            $configuration = array_map(
                static function (string $iri): string { return $this->iriConverter->getItemFromIri($iri)->getCode();},
                $ruleData['configuration']
            );

            $CatalogPromotionRule->setConfiguration($configuration);

            $catalogPromotion->addRule($CatalogPromotionRule);
        }

        return $catalogPromotion;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CatalogPromotion::class === $to;
    }
}
