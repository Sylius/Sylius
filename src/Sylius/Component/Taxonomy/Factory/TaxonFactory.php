<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxonFactory implements TaxonFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $taxonomyRepository;

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $taxonomyRepository
     */
    public function __construct(FactoryInterface $factory, RepositoryInterface $taxonomyRepository)
    {
        $this->factory = $factory;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForTaxonomy($taxonomyId)
    {
        if (null === $taxonomy = $this->taxonomyRepository->find($taxonomyId)) {
            throw new \InvalidArgumentException(sprintf('Taxonomy with id "%s" does not exist.', $taxonomyId));
        }

        $coupon = $this->factory->createNew();
        $coupon->setTaxonomy($taxonomy);

        return $coupon;
    }
}
