<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

/**
 * Settings parameter interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface ParameterInterface
{
    public function getId();
    public function getNamespace();
    public function setNamespace($namespace);
    public function getName();
    public function setName($name);
    public function getValue();
    public function setValue($value);
    public function getModifiedAt();
}
