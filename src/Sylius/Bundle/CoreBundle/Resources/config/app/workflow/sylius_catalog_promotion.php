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
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $catalogPromotion = $framework->workflows()->workflows('sylius_catalog_promotion');
    $catalogPromotion
        ->type('state_machine')
        ->supports([CatalogPromotion::class])
        ->initialMarking(['active']);

    $catalogPromotion->markingStore()
        ->type('method')
        ->property('state');

    $catalogPromotion->place()->name('active');
    $catalogPromotion->place()->name('inactive');
    $catalogPromotion->place()->name('processing');

    $catalogPromotion->transition()
        ->name('activate')
        ->from(['processing'])
        ->to(['active']);

    $catalogPromotion->transition()
        ->name('deactivate')
        ->from(['processing'])
        ->to(['inactive']);

    $catalogPromotion->transition()
        ->name('process')
        ->from(['inactive', 'active'])
        ->to(['processing']);
};
