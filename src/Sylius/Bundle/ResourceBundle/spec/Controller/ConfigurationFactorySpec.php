<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class ConfigurationFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('paginate' => 10));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ConfigurationFactory');
    }

    function it_should_create_configuration()
    {
        $this->createConfiguration(
            'sylius',
            'product',
            'SyliusWebBundle:Product',
            'twig',
            array('paginate' => 10)
        )->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\Configuration');
    }
}
