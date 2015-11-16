<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Transformer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceToIdentifierTransformerSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'name');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Transformer\ResourceToIdentifierTransformer');
    }

    function it_should_implement_parameter_transformer_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface');
    }
}
