<?php


namespace Sylius\Bundle\ApiBundle\Filter;


class ImageFilters
{
    /** @var array */
    private $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
