<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Model;

use Pagerfanta\Pagerfanta;

class ResultSet implements ResultSetInterface
{
    /** @var string */
    private $type;

    /** @var Pagerfanta */
    private $pagerfanta;

    public function __construct(string $type, Pagerfanta $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pagerfanta;
    }
}
