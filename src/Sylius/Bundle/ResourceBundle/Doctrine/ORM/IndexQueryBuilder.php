<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class IndexQueryBuilder extends QueryBuilder
{
    protected $index;

    public function forceIndex(string $indexName): self
    {
        $this->index = $indexName;

        return $this;
    }

    public function getQuery()
    {
        $query = parent::getQuery();

        if ($this->index !== null) {
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, UseIndexWalker::class);
            $query->setHint(UseIndexWalker::HINT_USE_INDEX, $this->index);
        }

        return $query;
    }
}
