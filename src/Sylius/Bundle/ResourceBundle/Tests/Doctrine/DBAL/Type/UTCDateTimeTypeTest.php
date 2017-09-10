<?php

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Tests\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\DBAL\Type\UTCDateTimeType;

final class UTCDateTimeTypeTest extends TestCase
{
    /**
     * @var UTCDateTimeType
     */
    private $type;

    /**
     * @var AbstractPlatform
     */
    private $abstractPlatform;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        Type::overrideType(Type::DATETIME, UTCDateTimeType::class);

        $this->type = Type::getType(Type::DATETIME);

        $this->abstractPlatform = $this->createMock(AbstractPlatform::class);
        $this->abstractPlatform->method('getDateTimeFormatString')->willReturn('Y-m-d H:i:s');
    }

    /**
     * @test
     */
    public function it_converts_datetime_object_to_UTC_based_database_value()
    {
        Assert::assertSame(
            '2016-12-31 19:00:00',
            $this->type->convertToDatabaseValue(
                \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('GMT+0600')),
                $this->abstractPlatform
            )
        );

        Assert::assertSame(
            '2016-12-31 19:00:00',
            $this->type->convertToDatabaseValue(
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('GMT+0600')),
                $this->abstractPlatform
            )
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_while_converting_null_to_UTC_based_database_value()
    {
        Assert::assertNull($this->type->convertToDatabaseValue(null, $this->abstractPlatform));
    }

    /**
     * @test
     */
    public function it_converts_UTC_based_database_value_to_datetime_object()
    {
        Assert::assertEquals(
            \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('UTC')),
            $this->type->convertToPHPValue('2017-01-01 01:00:00', $this->abstractPlatform)
        );

        Assert::assertEquals(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('UTC')),
            $this->type->convertToPHPValue('2017-01-01 01:00:00', $this->abstractPlatform)
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_while_converting_null_to_datetime_object()
    {
        Assert::assertNull($this->type->convertToPHPValue(null, $this->abstractPlatform));
    }

    /**
     * @test
     */
    public function it_does_nothing_while_converting_datetime_object_to_datetime_object()
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('GMT+0600'));
        Assert::assertSame($date, $this->type->convertToPHPValue($date, $this->abstractPlatform));

        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-01-01 01:00:00', new \DateTimeZone('GMT+0600'));
        Assert::assertSame($date, $this->type->convertToPHPValue($date, $this->abstractPlatform));
    }

    /**
     * @test
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function it_throws_an_exception_while_converting_invalid_date_to_datetime_object()
    {
        $this->type->convertToPHPValue('2017-02-30T01:00:00', $this->abstractPlatform);
    }
}
