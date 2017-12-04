<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\ORM\ORMException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ResourceDeleteHandlerSpec extends ObjectBehavior
{
    function let(ResourceDeleteHandlerInterface $decoratedHandler): void
    {
        $this->beConstructedWith($decoratedHandler);
    }

    function it_implements_resource_delete_handler_interface(): void
    {
        $this->shouldImplement(ResourceDeleteHandlerInterface::class);
    }

    function it_uses_decorated_handler_to_handle_resource_deletion(
        ResourceDeleteHandlerInterface $decoratedHandler,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $decoratedHandler->handle($resource, $repository)->shouldBeCalled();

        $this->handle($resource, $repository);
    }

    function it_throws_delete_handling_exception_if_something_gone_wrong_with_orm_while_deleting_resource(
        ResourceDeleteHandlerInterface $decoratedHandler,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $decoratedHandler->handle($resource, $repository)->willThrow(ORMException::class);

        $this->shouldThrow(DeleteHandlingException::class)->during('handle', [$resource, $repository]);
    }
}
