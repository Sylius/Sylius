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

use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PaymentMethodRepository extends TranslatableResourceRepository implements PaymentMethodRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderForChoiceType(array $options)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        if (isset($options['disabled']) && !$options['disabled']) {
            $queryBuilder->where('method.enabled = true');
        }

        return $queryBuilder;
    }

    protected function getAlias()
    {
        return 'method';
    }
}
