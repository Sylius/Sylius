<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\ODM\PHPCR\DocumentManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StaticContentRepositorySpec extends ObjectBehavior
{
    function let(DocumentRepository $objectRepository, DocumentManager $objectManager)
    {
        $this->beConstructedWith($objectRepository, $objectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR\StaticContentRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Repository\ResourceRepositoryInterface');
    }

    function it_find_by_id(DocumentRepository $objectRepository)
    {
        $objectRepository->find('/cms/pages/id')->shouldBeCalled();

        $this->findStaticContent('id');
    }
}
