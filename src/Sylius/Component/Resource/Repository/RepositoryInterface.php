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

namespace Sylius\Component\Resource\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface RepositoryInterface extends ObjectRepository
{
    public const ORDER_ASCENDING = 'ASC';
    public const ORDER_DESCENDING = 'DESC';

    /**
     * @param array $criteria
     * @param array $sorting
     *
     * @return iterable
     */
    public function createPaginator(array $criteria = [], array $sorting = []): iterable;

    /**
     * @param ResourceInterface $resource
     */
    public function add(ResourceInterface $resource): void;

    /**
     * @param ResourceInterface $resource
     */
    public function remove(ResourceInterface $resource): void;
}
