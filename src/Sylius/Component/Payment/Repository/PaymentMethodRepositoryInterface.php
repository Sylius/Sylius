<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface PaymentMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return PaymentMethodInterface[]
     */
    public function findByName($name, $locale);
}
