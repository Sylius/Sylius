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

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
class AddProductReview implements CommandAwareDataTransformerInterface
{
    /**
     * @psalm-immutable
     *
     * @var string|null
     */
    public $title;

    /**
     * @psalm-immutable
     *
     * @var int|null
     */
    public $rating;

    /**
     * @psalm-immutable
     *
     * @var string|null
     */
    public $comment;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $productCode;

    /**
     * @psalm-immutable
     *
     * @var string|null
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
