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

namespace Sylius\Bundle\ApiBundle\Command\Catalog;

use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

/** @experimental */
class AddProductReview implements CommandAwareDataTransformerInterface, IriToIdentifierConversionAwareInterface
{
    /**
     * @var string
     * @psalm-immutable
     */
    public $title;

    /**
     * @var int
     * @psalm-immutable
     */
    public $rating;

    /**
     * @var string
     * @psalm-immutable
     */
    public $comment;

    /**
     * @var string
     * @psalm-immutable
     */
    public $productCode;

    /**
     * @var string|null
     * @psalm-immutable
     */
    public $email;

    public function __construct(
        ?string $title,
        ?int $rating,
        ?string $comment,
        string $productCode,
        ?string $email = null
    ) {
        $this->title = $title;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->productCode = $productCode;
        $this->email = $email;
    }
}
