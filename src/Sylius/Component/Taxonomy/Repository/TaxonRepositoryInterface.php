<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    public function findChildren(TaxonInterface $taxon);

    /**
     * @return array
     */
    public function findRootNodes();

    /**
     * @param string $permalink
     *
     * @return TaxonInterface
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByPermalink($permalink);
}
