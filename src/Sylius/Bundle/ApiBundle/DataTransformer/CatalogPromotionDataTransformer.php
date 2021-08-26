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

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionAction;

final class CatalogPromotionDataTransformer implements DataTransformerInterface
{

    public function __construct()
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setCode($object->getCode());
        $catalogPromotion->setName($object->getName());

        foreach ($object->actions as $actionData) {

            $catalogPromotionAction = new CatalogPromotionAction();

            $catalogPromotionAction->setCatalogPromotion($catalogPromotion);
            $catalogPromotionAction->setType($actionData['type']);

            $catalogPromotionAction->setConfiguration($actionData['configuration']);

            $catalogPromotion->addAction($catalogPromotionAction);
        }
        return $catalogPromotion;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CatalogPromotion::class === $to;
    }
}
