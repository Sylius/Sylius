<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Helper\PathHelper;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @mixin PathHelper
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class PathHelperSpec extends ObjectBehavior
{
	function let(ThemeContextInterface $themeContext)
    {
        $this->beConstructedWith($themeContext, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PathHelper::class);
    }

    function it_should_not_apply_suffix($themeContext)
    {
    	$paths = ['sylius/nested1', 'theme/nested2'];

    	$this->applySuffixFor($paths)->shouldReturn($paths);
    }

    function it_should_apply_suffix_to_paths($themeContext)
    {
    	$this->beConstructedWith($themeContext, true);

    	$themeContext->getName()->willReturn('suffix');
    	$paths = ['sylius/nested1', 'theme/nested2'];

    	$this->applySuffixFor($paths)->shouldReturn(['sylius/nested1/suffix', 'theme/nested2/suffix']);
    }

    function it_should_not_apply_suffix_to_paths_when_name_is_null_and_setting_not_enabled($themeContext)
    {
    	$themeContext->getName()->willReturn(null);
    	$paths = ['sylius/nested1', 'theme/nested2'];

    	$this->applySuffixFor($paths)->shouldReturn($paths);
    }

    function it_should_throw_an_exception($themeContext)
    {
    	$this->beConstructedWith($themeContext, true);
    	$themeContext->getName()->willReturn(null);

    	$this->shouldThrow(InvalidConfigurationException::class)
    		->duringApplySuffixFor(['sylius/nested1', 'theme/nested2']);
    }

    function it_should_return_empty_array($themeContext)
    {
    	$themeContext->getName()->willReturn('suffix');

    	$this->applySuffixFor([])->shouldReturn([]);
    }
}
