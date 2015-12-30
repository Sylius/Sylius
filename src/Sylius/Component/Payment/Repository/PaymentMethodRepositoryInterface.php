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

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface PaymentMethodRepositoryInterface
{
    /**
     * @param array $options
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderForChoiceType(array $options);

    /**
     * @param array $names
     *
     * @return PaymentMethodInterface[]
     */
    public function findByName(array $names);

    /**
     * @param string $name
     *
     * @return PaymentMethodInterface|null
     */
    public function findOneByName($name);
}

