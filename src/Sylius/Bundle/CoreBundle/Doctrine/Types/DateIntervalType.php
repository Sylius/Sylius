<?php

namespace Sylius\Bundle\CoreBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DateIntervalType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if (!$platform->hasDoctrineTypeMappingFor('varchar')) {
            throw new \RuntimeException("Your database does not support VARCHAR, and cannot use DBAL mapping type DateIntervalType.");
        }

        return $platform->getVarcharTypeDeclarationSQL(array());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || !$value instanceof \DateInterval) {
            return null;
        }

        return $value->format('P%yY%mM%dDT%hH%iM%sS');
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return null;
        }

        return new \DateInterval($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_date_interval';
    }
}
