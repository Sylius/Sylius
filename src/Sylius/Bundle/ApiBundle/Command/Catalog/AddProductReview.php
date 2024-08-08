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

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailAwareInterface;

readonly class AddProductReview implements IriToIdentifierConversionAwareInterface, LoggedInCustomerEmailAwareInterface
{
    public function __construct(
        public string $title,
        public int $rating,
        public string $comment,
        public string $productCode,
        public ?string $email = null,
    ) {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
