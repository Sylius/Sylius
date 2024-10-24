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

namespace Sylius\Bundle\ApiBundle\Command\Catalog;

use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

#[LoggedInCustomerEmailAware]
class AddProductReview implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        public readonly string $title,
        public readonly int $rating,
        public readonly string $comment,
        public readonly string $productCode,
        public readonly ?string $email = null,
    ) {
    }
}
