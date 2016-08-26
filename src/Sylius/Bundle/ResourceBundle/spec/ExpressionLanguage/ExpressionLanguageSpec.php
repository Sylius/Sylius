<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\ExpressionLanguage;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionLanguageSpec extends ObjectBehavior
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
