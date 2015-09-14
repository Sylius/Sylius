<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Form\Transformer;

use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Review\Model\ReviewAuthorInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ReviewAuthorTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof ReviewAuthorInterface) {
            throw new UnexpectedTypeException($value, 'Sylius\Component\Review\Model\ReviewAuthorInterface');
        }

        return $value->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            return;
        }

        $customer = new Customer();
        $customer->setEmail($value);

        return $customer;
    }
}
