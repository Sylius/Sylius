<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\Model;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 * @author Mateusz Zalewski <zaleslaw@gmail.com>
 */

class Report
{
    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $description;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
