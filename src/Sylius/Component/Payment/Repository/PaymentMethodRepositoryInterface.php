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
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface PaymentMethodRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * @param array $options
     *
     * @return mixed
     */
    public function getQueryBuidlerForChoiceType(array $options);
}
