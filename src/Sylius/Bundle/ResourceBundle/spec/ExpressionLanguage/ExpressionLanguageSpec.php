<?php

namespace spec\Sylius\Bundle\ResourceBundle\ExpressionLanguage;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExpressionLanguageSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\ExpressionLanguage\ExpressionLanguage');
    }

    public function it_is_expression_language()
    {
        $this->shouldHaveType('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
    }

    public function it_is_container_aware(ContainerInterface $container)
    {
        $this->setContainer($container)->shouldReturn($this);
    }
}
