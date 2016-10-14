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
use Sylius\Bundle\ResourceBundle\Controller\Parameters;

/**
 * @mixin Parameters
 *
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
final class ParametersSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Parameters::class);
    }

    function it_has_mutable_parameters()
    {
        $this->replace([]);
    }

    function it_has_parameters()
    {
        $this->replace([
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ]);

        $this->all()->shouldReturn([
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ]);
    }

    function it_gets_a_single_parameter_and_supports_default_value()
    {
        $this->replace([
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ]);

        $this->get('criteria')->shouldReturn('criteria');
        $this->get('sorting')->shouldReturn(null);
        $this->get('sorting', 'default')->shouldReturn('default');
    }
}
