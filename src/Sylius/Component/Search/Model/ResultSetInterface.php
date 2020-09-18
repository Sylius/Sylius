<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Model;

use Pagerfanta\Pagerfanta;

interface ResultSetInterface
{
    public function getType(): string;

    public function getPager(): Pagerfanta;
}
