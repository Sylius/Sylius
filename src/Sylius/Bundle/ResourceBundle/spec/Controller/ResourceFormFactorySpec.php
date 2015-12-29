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
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceFormFactorySpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith($formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceFormFactory');
    }
    
    function it_implements_resource_form_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface');
    }

    function it_creates_appropriate_form_based_on_configuration(
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        FormFactoryInterface $formFactory,
        FormInterface $form
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getFormType()->willReturn('sylius_product_pricing');
        $formFactory->create('sylius_product_pricing', $resource)->willReturn($form);
        
        $this->create($requestConfiguration, $resource)->shouldReturn($form);
    }

    function it_creates_form_without_root_name_and_disables_csrf_protection_for_non_html_requests(
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        FormFactoryInterface $formFactory,
        FormInterface $form
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getFormType()->willReturn('sylius_product_api');
        $formFactory->createNamed('', 'sylius_product_api', $resource, array('csrf_protection' => false))->willReturn($form);

        $this->create($requestConfiguration, $resource)->shouldReturn($form);
    }
}
