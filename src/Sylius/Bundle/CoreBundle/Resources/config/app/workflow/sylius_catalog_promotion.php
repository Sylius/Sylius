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

use Sylius\Component\Core\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $catalogPromotion = $framework->workflows()->workflows('sylius_catalog_promotion');
    $catalogPromotion
        ->type('state_machine')
        ->supports([CatalogPromotion::class])
        ->initialMarking([CatalogPromotionStates::STATE_INACTIVE]);

    $catalogPromotion->markingStore()
        ->type('method')
        ->property('state');

    $catalogPromotion->place()->name(CatalogPromotionStates::STATE_ACTIVE);
    $catalogPromotion->place()->name(CatalogPromotionStates::STATE_INACTIVE);
    $catalogPromotion->place()->name(CatalogPromotionStates::STATE_PROCESSING);

    $catalogPromotion->transition()
        ->name(CatalogPromotionTransitions::TRANSITION_ACTIVATE)
        ->from([CatalogPromotionStates::STATE_PROCESSING])
        ->to([CatalogPromotionStates::STATE_ACTIVE])
    ;

    $catalogPromotion->transition()
        ->name(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)
        ->from([CatalogPromotionStates::STATE_PROCESSING])
        ->to([CatalogPromotionStates::STATE_INACTIVE])
    ;

    $catalogPromotion->transition()
        ->name(CatalogPromotionTransitions::TRANSITION_PROCESS)
        ->from([CatalogPromotionStates::STATE_INACTIVE, CatalogPromotionStates::STATE_ACTIVE])
        ->to([CatalogPromotionStates::STATE_PROCESSING])
    ;
};
