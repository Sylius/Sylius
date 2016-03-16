<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Resolver;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxRateResolver implements TaxRateResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $taxRateRepository;

    /**
     * @var RepositoryInterface
     */
    public function __construct(RepositoryInterface $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(TaxableInterface $taxable, array $criteria = [])
    {
        if (null === $category = $taxable->getTaxCategory()) {
            return null;
        }

        $criteria = array_merge(['category' => $category], $criteria);

        return $this->taxRateRepository->findOneBy($criteria);
    }
}
