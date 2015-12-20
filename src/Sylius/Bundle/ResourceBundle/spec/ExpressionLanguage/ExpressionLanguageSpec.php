<?php

namespace spec\Sylius\Bundle\ResourceBundle\ExpressionLanguage;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionLanguageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage');
    }

    function it_is_expression_language()
    {
        $this->shouldHaveType(ExpressionLanguage::class);
    }

    function it_is_container_aware(ContainerInterface $container)
    {
        $this->setContainer($container)->shouldReturn($this);
    }
}
