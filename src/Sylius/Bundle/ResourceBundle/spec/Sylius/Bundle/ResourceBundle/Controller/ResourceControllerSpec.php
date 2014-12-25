<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Resource controller spec.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceControllerSpec extends ObjectBehavior
{
    function let(
        Configuration $configuration,
        ContainerInterface $container,
        RouterInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        ObjectManager $objectManager,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory
    ) {
        $this->beConstructedWith($configuration);
        $configuration->isApiRequest()->willReturn(false);

        $configuration->getServiceName('manager')->willReturn('some_manager');
        $container->get('router')->willReturn($router);
        $container->get('session')->willReturn($session);
        $container->get('translator')->willReturn($translator);
        $container->get('some_manager')->willReturn($objectManager);
        $container->get('event_dispatcher')->willReturn($eventDispatcher);
        $container->get('form.factory')->willReturn($formFactory);

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }

    function it_is_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }

    function it_gets_form_from_class_name(
        Configuration $configuration,
        FormFactoryInterface $formFactory,
        FormInterface $form
    ) {
        $formClass = 'spec\Sylius\Bundle\ResourceBundle\Controller\TestFormType';
        $configuration->getFormType()->willReturn($formClass);
        $formFactory->create(Argument::type($formClass), Argument::cetera())->shouldBeCalled()->willReturn($form);

        $this->getForm()->shouldReturn($form);
    }
}

class TestFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_test_form';
    }
}
