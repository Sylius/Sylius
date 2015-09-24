<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Compiler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @mixin \Sylius\Component\Metadata\Compiler\MetadataCompiler
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataCompilerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Compiler\MetadataCompiler');
    }

    function it_implements_Metadata_Compiler_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Compiler\MetadataCompilerInterface');
    }

    function it_clones_metadata_even_if_nothing_changes(
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata
    ) {
        $childMetadata->merge($parentMetadata)->shouldBeCalled();

        $this->compile($childMetadata, [$parentMetadata])->shouldBeLike($childMetadata);
        $this->compile($childMetadata, [$parentMetadata])->shouldNotReturn($childMetadata);
    }

    function it_does_not_handle_exceptions_thrown_while_merging_metadata(
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata
    ) {
        $childMetadata->merge($parentMetadata)->shouldBeCalled()->willThrow('\InvalidArgumentException');

        $this->shouldThrow('\InvalidArgumentException')->duringCompile($childMetadata, [$parentMetadata]);
    }
}
