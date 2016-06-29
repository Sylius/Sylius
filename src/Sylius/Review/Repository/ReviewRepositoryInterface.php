<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Review\Repository;

use Sylius\Resource\Repository\RepositoryInterface;
use Sylius\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $reviewSubjectId
     * @param int $limit
     *
     * @return ReviewInterface[]
     */
    public function findAcceptedBySubjectId($reviewSubjectId, $limit);
}
