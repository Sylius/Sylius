<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Pawel Jedrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Resolver;

use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * Tax rate resolver interface.
 *
 * @author Pawel Jedrzejewski <pawel@sylius.org>
 */
interface TaxRateResolverInterface
{
    /**
     * Get the tax rate(s) for given taxable good and context.
     *
     * @param TaxableInterface $taxable
     * @param array            $criteria
     *
     * @return null|TaxRateInterface[]
     */
    public function resolve(TaxableInterface $taxable, array $criteria = array());
}

