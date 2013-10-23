<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

/**
 * Add by default the `%prefix%_%bundleName%_` prefix to every database table
 */
class NamingStrategy extends UnderscoreNamingStrategy
{
    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        parent::__construct();

        $this->prefix = trim($prefix, '_-');
    }

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        $class = parent::classToTableName($className);
        if (false !== preg_match('#([a-z]+)Bundle#i', $className, $result) && isset($result[1])) {
            $bundle = strtolower($result[1]);
            if (0 !== strpos($class, $bundle.'_')) {
                return sprintf('%s_%s_%s', $this->prefix, $bundle, $class);
            }
        }

        return sprintf('%s_%s', $this->prefix, $class);
    }
}
