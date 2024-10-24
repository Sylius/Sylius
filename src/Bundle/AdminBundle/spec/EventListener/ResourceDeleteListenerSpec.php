<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class ResourceDeleteListenerSpec extends ObjectBehavior
{
    function it_does_nothing_if_exception_is_not_foreign_key_constraint(KernelInterface $kernel): void
    {
        $this->onResourceDelete(new ExceptionEvent($kernel->getWrappedObject(), new Request(), HttpKernelInterface::MAIN_REQUEST, new \Exception()))->shouldReturn(null);
    }

    function it_does_nothing_if_request_is_not_main(KernelInterface $kernel, ForeignKeyConstraintViolationException $exception): void
    {
        $event = new ExceptionEvent($kernel->getWrappedObject(), new Request(), HttpKernelInterface::SUB_REQUEST, $exception->getWrappedObject());

        $this->onResourceDelete($event)->shouldReturn(null);
    }

    function it_throws_resource_delete_exception_if_all_conditions_are_met(KernelInterface $kernel, ForeignKeyConstraintViolationException $exception): void
    {
        $event = new ExceptionEvent($kernel->getWrappedObject(), new Request(), HttpKernelInterface::MAIN_REQUEST, $exception->getWrappedObject());

        $this->onResourceDelete($event)->shouldThrow(ResourceDeleteException::class);
    }

    function it_does_nothing_if_method_is_not_delete(KernelInterface $kernel, ForeignKeyConstraintViolationException $exception): void
    {
        $request = new Request([], [], ['_route' => 'sylius_admin_product_delete', '_sylius' => ['section' => 'admin']]);
        $request->setMethod('GET');

        $event = new ExceptionEvent($kernel->getWrappedObject(), $request, HttpKernelInterface::MAIN_REQUEST, $exception->getWrappedObject());

        $this->onResourceDelete($event)->shouldReturn(null);
    }

    function it_does_nothing_if_route_is_not_sylius(KernelInterface $kernel, ForeignKeyConstraintViolationException $exception): void
    {
        $request = new Request([], [], ['_route' => 'non_sylius_route', '_sylius' => ['section' => 'admin']]);

        $event = new ExceptionEvent($kernel->getWrappedObject(), $request, HttpKernelInterface::MAIN_REQUEST, $exception->getWrappedObject());

        $this->onResourceDelete($event)->shouldReturn(null);
    }

    function it_does_nothing_if_section_is_not_admin(KernelInterface $kernel, ForeignKeyConstraintViolationException $exception): void
    {
        $request = new Request([], [], ['_route' => 'sylius_admin_product_delete', '_sylius' => ['section' => 'shop']]);

        $event = new ExceptionEvent($kernel->getWrappedObject(), $request, HttpKernelInterface::MAIN_REQUEST, $exception->getWrappedObject());

        $this->onResourceDelete($event)->shouldReturn(null);
    }
}
