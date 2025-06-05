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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common\ResourceRemoveProcessor;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Model\Promotion;
use Sylius\Component\Core\Model\ShippingMethod;

final class ResourceRemoveProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $decoratedRemoveProcessor;

    private ResourceRemoveProcessor $resourceRemoveProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedRemoveProcessor = $this->createMock(ProcessorInterface::class);
        $this->resourceRemoveProcessor = new ResourceRemoveProcessor($this->decoratedRemoveProcessor);
    }

    public function testProcessesDataWithoutExceptions(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);

        $data = new Promotion();

        $this->decoratedRemoveProcessor->expects(self::once())
            ->method('process')
            ->with($data, $operationMock, [], []);

        $this->resourceRemoveProcessor->process($data, $operationMock, [], []);
    }

    public function testThrowsAResourceDeleteExceptionOnForeignKeyViolation(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);

        $data = new ShippingMethod();

        $driverException = $this->getMockBuilder(\Doctrine\DBAL\Driver\Exception::class)
            ->getMock();

        $foreignKeyException = $this->getMockBuilder(ForeignKeyConstraintViolationException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->decoratedRemoveProcessor->expects(self::once())
            ->method('process')
            ->with($data, $operationMock, [], [])
            ->willThrowException($foreignKeyException);

        self::expectException(ResourceDeleteException::class);

        $this->resourceRemoveProcessor->process($data, $operationMock, [], []);
    }
}
