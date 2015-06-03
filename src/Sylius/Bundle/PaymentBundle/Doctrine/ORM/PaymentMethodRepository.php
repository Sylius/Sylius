<?php


/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PaymentMethodRepository extends EntityRepository implements PaymentMethodRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQueryBuidlerByStatus($disabled)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        if (!$disabled) {
            $queryBuilder->where('method.enabled = true');
        }

        return $queryBuilder;
    }
}
