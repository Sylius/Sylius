<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Twig;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper;
use Sylius\Component\Order\Model\AdjustmentInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AggregateAdjustmentsExtensionSpec extends ObjectBehavior
{
    function let(AdjustmentsHelper $adjustmentsHelper)
    {
        $this->beConstructedWith($adjustmentsHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Twig\AggregateAdjustmentsExtension');
    }

    function it_is_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_has_functions()
    {
        $this->getFunctions()->shouldHaveFunction(new \Twig_SimpleFunction('sylius_aggregate_adjustments', [$this, 'aggregateAdjustments']));
    }

    function it_uses_helper_to_get_aggregated_adjustments(
        $adjustmentsHelper,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3
    ) {
        $adjustmentsHelper
            ->getAggregatedAdjustments([$adjustment1, $adjustment2, $adjustment3])
            ->willReturn(['tax 1' => 1000, 'tax2' => 500])
        ;

        $this->aggregateAdjustments([$adjustment1, $adjustment2, $adjustment3])->shouldReturn(['tax 1' => 1000, 'tax2' => 500]);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_aggregate_adjustments');
    }

    public function getMatchers()
    {
        return [
            'haveFunction' => function ($subject, $key) {

                if (!is_array($subject)) {
                    throw new FailureException('Subject of "hasFunction" matcher must be an array');
                }

                if (!$key instanceof \Twig_SimpleFunction) {
                    throw new FailureException('Key of "hasFunction" matcher must be \Twig_SimpleFunction object');
                }

                /** @var \Twig_SimpleFunction $subjectElement */
                foreach ($subject as $subjectElement) {
                    if ($subjectElement->getName() === $key->getName() && $subjectElement->getCallable()[1] === $key->getCallable()[1]) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
