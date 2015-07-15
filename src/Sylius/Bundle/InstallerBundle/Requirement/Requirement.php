<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Requirement;

class Requirement
{
    protected $label;
    protected $fulfilled;
    protected $expected;
    protected $actual;
    protected $help;
    protected $required;

    public function __construct($label, $fulfilled, $expected, $actual, $required = true, $help = null)
    {
        $this->label = $label;
        $this->fulfilled = (Boolean) $fulfilled;
        $this->expected = $expected;
        $this->actual = $actual;
        $this->required = (Boolean) $required;
        $this->help = $help;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    public function getExpected()
    {
        return $this->expected;
    }

    public function getActual()
    {
        return $this->actual;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function getHelp()
    {
        return $this->help;
    }
}
