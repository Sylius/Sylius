<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

class Requirement
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $fulfilled;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var string|null
     */
    protected $help;

    /**
     * @param string $label
     * @param bool $fulfilled
     * @param bool $required
     * @param string|null $help
     */
    public function __construct($label, $fulfilled, $required = true, $help = null)
    {
        $this->label = $label;
        $this->fulfilled = (boolean) $fulfilled;
        $this->required = (boolean) $required;
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }
}
