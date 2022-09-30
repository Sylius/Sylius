<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithCommentInterface
{
    public function withComment(string $comment): static;
}
