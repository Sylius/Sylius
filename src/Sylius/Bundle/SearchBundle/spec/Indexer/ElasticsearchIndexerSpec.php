<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Indexer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Kernel\Kernel;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchIndexerSpec extends ObjectBehavior
{
    public function let(
        Kernel $kernel
    ) {
        $this->beConstructedWith(
            $kernel
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Indexer\ElasticsearchIndexer');
    }

    public function it_implements_the_indexer_interface_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SearchBundle\Indexer\IndexerInterface');
    }
}
