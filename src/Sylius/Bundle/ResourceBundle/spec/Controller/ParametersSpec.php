<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;

final class ParametersSpec extends ObjectBehavior
{
    public function it_has_mutable_parameters(): void
    {
        $this->replace([]);
    }

    public function it_has_parameters(): void
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

    public function it_gets_a_single_parameter_and_supports_default_value(): void
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
