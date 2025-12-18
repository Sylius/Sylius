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

namespace Tests\Sylius\Bundle\AdminBundle\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteListener;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ResourceDeleteListenerTest extends TestCase
{
    private ResourceDeleteListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ResourceDeleteListener();
    }

    public function testThrowsResourceDeleteExceptionIfAllConditionsAreMet(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = $this->createMock(ForeignKeyConstraintViolationException::class);

        $request = new Request([], [], [
            '_route' => 'sylius_admin_product_delete',
            '_sylius' => ['section' => 'admin'],
            '_controller' => 'ResourceController',
        ]);
        $request->setMethod('DELETE');
        $request->setRequestFormat('html');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $this->expectException(ResourceDeleteException::class);

        $this->listener->onResourceDelete($event);
    }
}
