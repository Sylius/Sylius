<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SearchBundle\Indexer\OrmIndexer;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmListenerSpec extends ObjectBehavior
{
    function let(OrmIndexer $ormIndexer)
    {
        $this->beConstructedWith($ormIndexer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Listener\OrmListener');
    }

    function it_populates_the_insertion_array(LifecycleEventArgs $args)
    {
        $this->postPersist($args);

        $this->scheduledForInsertion->shouldBeArray();
    }

    function it_populates_the_update_array(LifecycleEventArgs $args)
    {
        $this->postUpdate($args);

        $this->scheduledForInsertion->shouldBeArray();
    }

    function it_populates_the_delete_array(LifecycleEventArgs $args)
    {
        $this->preRemove($args);

        $this->scheduledForDeletion->shouldBeArray();
    }
}
