<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSettingsSchemaProvider implements ThemeSettingsSchemaProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSchema(ThemeInterface $theme)
    {
        $schemaPath = sprintf('%s/Settings.php', $theme->getPath());

        if (!file_exists($schemaPath)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find settings schema of theme "%s" (%s) in file "%s"',
                $theme->getTitle(),
                $theme->getName(),
                $schemaPath
            ));
        }

        $schema = require $schemaPath;

        if (!$schema instanceof SchemaInterface) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" must return an instance of "%s"',
                $schemaPath,
                SchemaInterface::class
            ));
        }

        return $schema;
    }
}
