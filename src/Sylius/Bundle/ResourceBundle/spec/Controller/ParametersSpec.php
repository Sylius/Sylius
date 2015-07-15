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
class ParametersSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\Parameters');
    }

    public function its_parameters_is_mutable()
    {
        $this->replace(array());
    }

    public function it_has_parameters()
    {
        $this->replace(array(
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ));

        $this->all()->shouldReturn(array(
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ));
    }

    public function it_get_parameter()
    {
        $this->replace(array(
            'criteria' => 'criteria',
            'paginate' => 'paginate',
        ));

        $this->get('criteria')->shouldReturn('criteria');
        $this->get('sorting')->shouldReturn(null);
        $this->get('sorting', 'default')->shouldReturn('default');
    }
}
