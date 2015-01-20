<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Report\DataFetcher;

use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DelegatingRendererSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\Renderer\DelegatingRenderer');
    }

    function it_implements_delegating_renderer_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\Renderer\DelegatingRendererInterface');
    }

    function it_should_throw_exception_if_report_has_no_renderer_defined(ReportInterface $subject)
    {
        $subject->getDataFetcher()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot render data for ReportInterface instance without renderer defined.'))
            ->duringRender($subject)
        ;
    }
}