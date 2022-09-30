<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithCommentTrait
{
    public function withComment(string $comment): static
    {
        return $this->addState(['comment' => $comment]);
    }
}
