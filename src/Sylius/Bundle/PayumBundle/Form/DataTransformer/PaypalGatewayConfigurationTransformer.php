<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaypalGatewayConfigurationTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (empty($value)) {
            return $value;
        }

        $value['payum_http_client'] = $value['payum.http_client'];
        unset($value['payum.http_client']);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $value['payum.http_client'] = $value['payum_http_client'];
        unset($value['payum_http_client']);

        return $value;
    }
}