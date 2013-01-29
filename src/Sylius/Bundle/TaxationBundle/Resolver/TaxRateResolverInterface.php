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

use Sylius\Bundle\TaxationBundle\Model\TaxRateInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxableInterface;

/**
 * Tax rate resolver interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxRateResolverInterface
{
    /**
     * Get the tax rate for given taxable good and context.
     *
     * @param TaxableInterface $taxable
     * @param array            $criteria
     *
     * @return null|TaxRateInterface
     */
    public function resolve(TaxableInterface $taxable, array $criteria = array());
}
