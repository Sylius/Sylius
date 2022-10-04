<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithCommentTrait
{
    public function withComment(string $comment): self
    {
        return $this->addState(['comment' => $comment]);
    }
}
