<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Seo\Compiler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Seo\Model\MetadataInterface;
use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @mixin \Sylius\Component\Seo\Compiler\MetadataCompiler
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataCompilerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Seo\Compiler\MetadataCompiler');
    }

    function it_implements_Metadata_Compiler_interface()
    {
        $this->shouldImplement('Sylius\Component\Seo\Compiler\MetadataCompilerInterface');
    }

    function it_clones_metadata_even_if_nothing_changes(
        RootMetadataInterface $childRootMetadata,
        RootMetadataInterface $parentRootMetadata,
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata
    ) {
        $childMetadata->merge($parentMetadata)->shouldBeCalled();

        $childRootMetadata->hasParent()->shouldBeCalled()->willReturn(true);
        $childRootMetadata->getParent()->shouldBeCalled()->willReturn($parentRootMetadata);
        $childRootMetadata->getMetadata()->shouldBeCalled()->willReturn($childMetadata);

        $parentRootMetadata->hasParent()->shouldBeCalled()->willReturn(false);
        $parentRootMetadata->getMetadata()->shouldBeCalled()->willReturn($parentMetadata);

        $this->compile($childRootMetadata)->shouldBeLike($childMetadata);
        $this->compile($childRootMetadata)->shouldNotReturn($childMetadata);
    }

    function it_does_not_handle_exceptions_thrown_while_merging_metadata(
        RootMetadataInterface $childRootMetadata,
        RootMetadataInterface $parentRootMetadata,
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata
    ) {
        $childMetadata->merge($parentMetadata)->shouldBeCalled()->willThrow('\InvalidArgumentException');

        $childRootMetadata->hasParent()->shouldBeCalled()->willReturn(true);
        $childRootMetadata->getParent()->shouldBeCalled()->willReturn($parentRootMetadata);
        $childRootMetadata->getMetadata()->shouldBeCalled()->willReturn($childMetadata);

        $parentRootMetadata->getMetadata()->shouldBeCalled()->willReturn($parentMetadata);

        $this->shouldThrow('\InvalidArgumentException')->duringCompile($childRootMetadata);
    }
}
