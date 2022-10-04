<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithCommentInterface
{
    /**
     * @return $this
     */
    public function withComment(string $comment): self;
}
