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

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

/** @experimental */
class AddProductReview implements IriToIdentifierConversionAwareInterface, CustomerEmailAwareInterface
{
    /**
     * @immutable
     *
     * @var string|null
     */
    public $title;

    /**
     * @immutable
     *
     * @var int|null
     */
    public $rating;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $comment;

    /**
     * @immutable
     *
     * @var string
     */
    public $productCode;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $email;

    public function __construct(
        ?string $title,
        ?int $rating,
        ?string $comment,
        string $productCode,
        ?string $email = null,
    ) {
        $this->title = $title;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->productCode = $productCode;
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
