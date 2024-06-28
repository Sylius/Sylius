<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin\Crud\Index;

interface SearchFilterElementInterface
{
    public function searchWith(string $phrase): void;
}
