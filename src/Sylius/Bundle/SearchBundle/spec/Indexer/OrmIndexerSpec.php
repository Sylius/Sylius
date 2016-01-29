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

use FOS\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SearchBundle\Indexer\IndexerInterface;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmIndexerSpec extends ObjectBehavior
{
    function let(
        $config,
        ModelToElasticaAutoTransformer $transformer
    ) {
        $this->beConstructedWith(
            (Array) $config,
            $transformer
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Indexer\OrmIndexer');
    }

    function it_implements_the_indexer_interface_interface()
    {
        $this->shouldImplement(IndexerInterface::class);
    }
}
