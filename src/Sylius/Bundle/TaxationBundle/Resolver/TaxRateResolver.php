<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Resolver;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\TaxationBundle\Model\TaxableInterface;

/**
 * Default tax rate resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateResolver implements TaxRateResolverInterface
{
    /**
     * Tax rate repository.
     *
     * @var ObjectRepository
     */
    protected $taxRateRepository;

    /**
     * Tax rate repository.
     *
     * @var ObjectRepository $taxRateRepository
     */
    public function __construct(ObjectRepository $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(TaxableInterface $taxable, array $criteria = array())
    {
        if (null === $category = $taxable->getTaxCategory()) {
            return null;
        }

        $criteria = array_merge(array('category' => $category), $criteria);

        return $this->taxRateRepository->findOneBy($criteria);
    }
}
