<?php

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

final class UTCDateTimeType extends DateTimeType
{
    /**
     * @var \DateTimeZone
     */
    private static $utc;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            $value = $value->setTimezone($this->getUTC());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTimeInterface
    {
        if (null === $value || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $converted = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, $this->getUTC());

        if (!$converted) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        return $converted;
    }

    private function getUTC(): \DateTimeZone
    {
        if (!self::$utc) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }
}
