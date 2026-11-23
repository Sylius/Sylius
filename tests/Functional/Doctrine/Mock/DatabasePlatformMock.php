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

namespace Sylius\Tests\Functional\Doctrine\Mock;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DatabasePlatformMock extends AbstractPlatform
{
    public function supportsIdentityColumns(): bool
    {
        return true;
    }

    public function getBooleanTypeDeclarationSQL(array $field): string
    {
        return 'BOOLEAN';
    }

    public function getIntegerTypeDeclarationSQL(array $field): string
    {
        return 'INT';
    }

    public function getBigIntTypeDeclarationSQL(array $field): string
    {
        return 'BIGINT';
    }

    public function getSmallIntTypeDeclarationSQL(array $field): string
    {
        return 'SMALLINT';
    }

    protected function _getCommonIntegerTypeDeclarationSQL(array $columnDef): string
    {
        return 'INT';
    }

    public function getVarcharTypeDeclarationSQL(array $field): string
    {
        return 'VARCHAR';
    }

    public function getClobTypeDeclarationSQL(array $field): string
    {
        return 'CLOB';
    }

    public function getName(): string
    {
        throw new \BadMethodCallException('Not implemented');
    }

    protected function initializeDoctrineTypeMappings()
    {
    }

    public function getBlobTypeDeclarationSQL(array $field)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getCurrentDatabaseExpression(): string
    {
        throw DBALException::notSupported(__METHOD__);
    }
}
