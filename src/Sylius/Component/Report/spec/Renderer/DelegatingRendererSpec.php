<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Report\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\DelegatingRendererInterface;
use Sylius\Component\Report\Renderer\RendererInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DelegatingRendererSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $serviceRegistryInterface)
    {
        $this->beConstructedWith($serviceRegistryInterface);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\Renderer\DelegatingRenderer');
    }

    public function it_implements_delegating_renderer_interface()
    {
        $this->shouldImplement(DelegatingRendererInterface::class);
    }

    public function it_delegates_renderer_to_report(
        $serviceRegistryInterface,
        ReportInterface $subject,
        RendererInterface $renderer,
        Data $data)
    {
        $subject->getRenderer()->willReturn('default_renderer');
        $subject->getRendererConfiguration()->willReturn([]);

        $serviceRegistryInterface->get('default_renderer')->willReturn($renderer);
        $renderer->render($subject, $data)->shouldBeCalled();

        $this->render($subject, $data);
    }

    public function it_should_throw_exception_if_report_has_no_renderer_defined(ReportInterface $subject, Data $data)
    {
        $subject->getRenderer()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot render data for ReportInterface instance without renderer defined.'))
            ->duringRender($subject, $data)
        ;
    }
}
