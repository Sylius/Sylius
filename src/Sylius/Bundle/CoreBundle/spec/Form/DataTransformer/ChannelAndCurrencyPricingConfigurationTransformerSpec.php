<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ChannelAndCurrencyPricingConfigurationTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ChannelAndCurrencyPricingConfigurationTransformerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('_');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelAndCurrencyPricingConfigurationTransformer::class);
    }

    function it_is_data_transformer()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_array_into_proper_calculator_configuration_array()
    {
        $flatCalculatorConfiguration = ['Web_USD' => 1000, 'Web_EUR' => 2000, 'Mobile_USD' => 20000, 'Mobile_EUR' => 1400];

        $this->reverseTransform($flatCalculatorConfiguration)->shouldReturn([
            'Web' => [
                'USD' => 1000,
                'EUR' => 2000,
            ],
            'Mobile' => [
                'USD' => 20000,
                'EUR' => 1400
            ],
        ]);
    }

    function it_transform_back_from_proper_calculator_configuration_to_flat_array()
    {
        $this->transform([
            'Web' => [
                'USD' => 1000,
                'EUR' => 2000,
            ],
            'Mobile' => [
                'USD' => 20000,
                'EUR' => 1400
            ],
        ])->shouldReturn(['Web_USD' => 1000, 'Web_EUR' => 2000, 'Mobile_USD' => 20000, 'Mobile_EUR' => 1400]);
    }

    function it_returns_empty_array_if_given_value_is_null()
    {
        $this->transform(null)->shouldReturn([]);
    }
}
