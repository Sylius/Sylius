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

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\ODM\PHPCR\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository;

class StaticContentRepositorySpec extends ObjectBehavior
{
    function let(DocumentManager $dm, ClassMetadata $class, UnitOfWork $unitOfWork)
    {
        $dm->getUnitOfWork()->shouldBeCalled()->willreturn($unitOfWork);

        $this->beConstructedWith($dm, $class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR\StaticContentRepository');
    }

    function it_is_a_document_repository()
    {
        $this->shouldHaveType(DocumentRepository::class);
    }

    function it_find_by_id($dm)
    {
        $dm->find(Argument::any(), '/cms/pages/id')->shouldBeCalled();

        $this->findStaticContent('id');
    }
}
