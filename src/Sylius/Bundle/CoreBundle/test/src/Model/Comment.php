<?php

namespace Sylius\Bundle\CoreBundle\Application\Model;

final class Comment
{
    public function __construct (
        private string $state = 'new',
    ) {
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
