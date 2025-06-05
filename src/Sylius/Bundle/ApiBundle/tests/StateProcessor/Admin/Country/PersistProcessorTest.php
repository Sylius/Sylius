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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country\PersistProcessor;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Exception\ResourceDeleteException;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private CountryProvincesDeletionCheckerInterface&MockObject $countryProvincesDeletionChecker;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->countryProvincesDeletionChecker = $this->createMock(CountryProvincesDeletionCheckerInterface::class);
        $this->persistProcessor = new PersistProcessor($this->processor, $this->countryProvincesDeletionChecker);
    }

    public function testThrowsAnExceptionIfObjectIsNotACountry(): void
    {
        /** @var HttpOperation|MockObject $operationMock */
        $operationMock = $this->createMock(HttpOperation::class);

        $this->countryProvincesDeletionChecker->expects(self::never())->method('isDeletable');

        $this->processor->expects(self::never())->method('process')->with($this->any());

        self::expectException(\InvalidArgumentException::class);

        $this->persistProcessor->process(new \stdClass(), $operationMock, [], []);
    }

    public function testUsesDecoratedDataPersisterToPersistCountry(): void
    {
        /** @var CountryInterface|MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);

        $operation = new Post();

        $uriVariables = [];

        $context = [];

        $this->countryProvincesDeletionChecker->expects(self::once())
            ->method('isDeletable')
            ->with($countryMock)
            ->willReturn(true);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($countryMock, $operation, $uriVariables, $context)
            ->willReturn($countryMock);

        self::assertSame($countryMock, $this->persistProcessor->process($countryMock, $operation, $uriVariables, $context));
    }

    public function testThrowsAnErrorIfTheProvinceWithinACountryIsInUse(): void
    {
        /** @var CountryInterface|MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);
        /** @var HttpOperation|MockObject $operationMock */
        $operationMock = $this->createMock(HttpOperation::class);

        $uriVariables = [];

        $context = [];

        $this->countryProvincesDeletionChecker->expects(self::once())
            ->method('isDeletable')
            ->with($countryMock)
            ->willReturn(false);

        $this->processor->expects(self::never())->method('process')->with($this->any());

        self::expectException(ResourceDeleteException::class);

        $this->persistProcessor->process($countryMock, $operationMock, $uriVariables, $context);
    }
}
