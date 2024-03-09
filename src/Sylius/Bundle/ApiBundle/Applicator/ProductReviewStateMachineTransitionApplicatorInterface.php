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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Component\Review\Model\ReviewInterface;

interface ProductReviewStateMachineTransitionApplicatorInterface
{
    public function accept(ReviewInterface $data): ReviewInterface;

    public function reject(ReviewInterface $data): ReviewInterface;
}
