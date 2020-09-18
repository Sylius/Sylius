<?php

declare(strict_types=1);

namespace Sylius\Component\Product\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Search\Repository\SearchableRepositoryInterface;

interface ProductAttributeRepositoryInterface extends RepositoryInterface, SearchableRepositoryInterface
{
}
