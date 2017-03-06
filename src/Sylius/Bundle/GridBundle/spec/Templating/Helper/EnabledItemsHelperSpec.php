<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Templating\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Templating\Helper\EnabledItemsHelper;
use Sylius\Component\Grid\Definition\Action;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class EnabledItemsHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EnabledItemsHelper::class);
    }

    function it_is_a_symfony_templating_helper()
    {
        $this->shouldImplement(HelperInterface::class);
    }

    function it_extends_a_base_symfony_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_only_enabled_items(Action $firstAction, Action $secondAction, Action $thirdAction)
    {
        $firstAction->isEnabled()->willReturn(true);
        $secondAction->isEnabled()->willReturn(false);
        $thirdAction->isEnabled()->willReturn(false);

        $this->getEnabledItems([$firstAction, $secondAction, $thirdAction])->shouldHaveCount(1);
    }

    function it_returns_only_disabled_items(Action $firstAction, Action $secondAction, Action $thirdAction)
    {
        $firstAction->isEnabled()->willReturn(true);
        $secondAction->isEnabled()->willReturn(false);
        $thirdAction->isEnabled()->willReturn(false);

        $this->getEnabledItems([$firstAction, $secondAction, $thirdAction], false)->shouldHaveCount(2);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_enabled_items');
    }
}
