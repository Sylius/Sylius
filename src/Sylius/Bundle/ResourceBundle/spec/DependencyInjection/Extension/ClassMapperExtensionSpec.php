<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ClassMapperExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ClassMapperExtension');
    }

    function it_is_extension()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ExtensionInterface');
    }

    function it_is_a_supported_extension()
    {
        $this->isSupported(4)->shouldReturn(true);
        $this->isSupported(5)->shouldReturn(false);
    }

    function it_set_form_class_to_the_container(ContainerBuilder $container)
    {
        $container->setParameter('sylius.form.type.product_default.class', 'My\Class');

        $this->configure(
            $container,
            array(
                'classes' => array(
                    'product' => array(
                        'form' => array(
                            'default' => 'My\Class'
                        ),
                    ),
                ),
            ),
            array('app_name' => 'sylius')
        );
    }

    function it_set_model_class_to_the_container(ContainerBuilder $container)
    {
        $container->setParameter('sylius.model.product.class', 'My\Class');

        $this->configure(
            $container,
            array(
                'classes' => array(
                    'product' => array(
                        'model' => 'My\Class'
                    ),
                ),
            ),
            array('app_name' => 'sylius')
        );
    }
}
