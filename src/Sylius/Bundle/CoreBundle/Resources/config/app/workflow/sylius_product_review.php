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

use Sylius\Component\Core\Model\ProductReview;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $productReview = $framework->workflows()->workflows('sylius_product_review');
    $productReview
        ->type('state_machine')
        ->supports([ProductReview::class])
        ->initialMarking(['new']);

    $productReview->markingStore()
        ->type('method')
        ->property('status');

    $productReview->place()->name('new');
    $productReview->place()->name('accepted');
    $productReview->place()->name('rejected');

    $productReview->transition()
        ->name('accept')
        ->from(['new'])
        ->to(['accepted']);

    $productReview->transition()
        ->name('reject')
        ->from(['new'])
        ->to(['rejected']);
};
