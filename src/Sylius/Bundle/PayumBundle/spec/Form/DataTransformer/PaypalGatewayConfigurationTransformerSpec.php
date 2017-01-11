<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Form\DataTransformer\PaypalGatewayConfigurationTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaypalGatewayConfigurationTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaypalGatewayConfigurationTransformer::class);
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_changes_paypal_gateway_configuration_to_be_handled_well_by_payum()
    {
        $this->reverseTransform([
            'username' => 'TEST',
            'password' => 'TEST',
            'signature' => 'TEST',
            'payum_http_client' => '@sylius.payum.http_client'
        ])->shouldReturn([
            'username' => 'TEST',
            'password' => 'TEST',
            'signature' => 'TEST',
            'payum.http_client' => '@sylius.payum.http_client'
        ]);
    }

    function it_changes_stored_configuration_to_be_properly_handled_by_form()
    {
        $this->transform([
            'username' => 'TEST',
            'password' => 'TEST',
            'signature' => 'TEST',
            'payum.http_client' => '@sylius.payum.http_client'
        ])->shouldReturn([
            'username' => 'TEST',
            'password' => 'TEST',
            'signature' => 'TEST',
            'payum_http_client' => '@sylius.payum.http_client'
        ]);
    }

    function it_does_not_transform_empty_array()
    {
        $this->transform([])->shouldReturn([]);
    }
}
