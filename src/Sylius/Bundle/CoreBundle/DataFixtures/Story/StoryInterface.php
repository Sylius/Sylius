<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

interface StoryInterface
{
    public function build(): void;
}
