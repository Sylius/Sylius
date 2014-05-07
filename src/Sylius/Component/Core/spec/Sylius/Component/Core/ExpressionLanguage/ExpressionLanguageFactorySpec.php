<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\ExpressionLanguage;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ExpressionLanguageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\ExpressionLanguage\ExpressionLanguageFactory');
    }

    function it_implements_Sylius_expression_language_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\ExpressionLanguage\ExpressionLanguageFactoryInterface');
    }

    function it_creates_new_expression_factory_instance()
    {
        $this->create()->shouldHaveType('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
    }
}
