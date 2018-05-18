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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class URLRedirect
 *
 * The entity to store URL redirects in Sylius
 */
class URLRedirect implements URLRedirectInterface, ResourceInterface
{
    const PERMANENT = 'PERMANENT';
    const TEMPORARY = 'TEMPORARY';
    const INVISIBLE = 'INVISIBLE';

    /** @var int */
    private $id;

    /** @var string */
    private $oldRoute;

    /** @var string */
    private $newRoute;

    /** @var bool */
    private $enabled;

    /** @var string */
    private $type;

    public function __construct(string $oldRoute = '', string $newRoute = '')
    {
        $this->oldRoute = $oldRoute;
        $this->newRoute = $newRoute;
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOldRoute(): string
    {
        return $this->oldRoute;
    }

    /**
     * @param string $oldRoute
     */
    public function setOldRoute(?string $oldRoute): void
    {
        $this->oldRoute = $oldRoute ?: '';
    }

    /**
     * @return string
     */
    public function getNewRoute(): string
    {
        return $this->newRoute;
    }

    /**
     * @param string $newRoute
     */
    public function setNewRoute(?string $newRoute): void
    {
        $this->newRoute = $newRoute ?: '';
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /** {@inheritdoc} */
    public function setType(?string $type): void
    {
        $type = null === $type ? self::PERMANENT : strtoupper($type);
        $allTypes = [self::PERMANENT, self::TEMPORARY, self::INVISIBLE];

        if (!in_array($type, $allTypes)) {
            $type = self::PERMANENT;
        }

        $this->type = $type;
    }
}
