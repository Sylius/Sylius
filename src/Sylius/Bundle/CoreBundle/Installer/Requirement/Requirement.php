<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

final class Requirement
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $fulfilled;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var string|null
     */
    private $help;

    /**
     * @param string $label
     * @param bool $fulfilled
     * @param bool $required
     * @param string|null $help
     */
    public function __construct(string $label, bool $fulfilled, bool $required = true, ?string $help = null)
    {
        $this->label = $label;
        $this->fulfilled = $fulfilled;
        $this->required = $required;
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isFulfilled(): bool
    {
        return $this->fulfilled;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return $this->help;
    }
}
