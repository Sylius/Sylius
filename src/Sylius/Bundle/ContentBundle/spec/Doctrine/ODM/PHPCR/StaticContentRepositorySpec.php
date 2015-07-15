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

class StaticContentRepositorySpec extends ObjectBehavior
{
    public function let(DocumentManager $dm, ClassMetadata $class, UnitOfWork $unitOfWork)
    {
        $dm->getUnitOfWork()->shouldBeCalled()->willreturn($unitOfWork);

        $this->beConstructedWith($dm, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Doctrine\ODM\PHPCR\StaticContentRepository');
    }

    public function it_is_a_document_repository()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository');
    }

    public function it_find_by_id($dm)
    {
        $dm->find(Argument::any(), '/cms/pages/id')->shouldBeCalled();

        $this->findStaticContent('id');
    }
}
