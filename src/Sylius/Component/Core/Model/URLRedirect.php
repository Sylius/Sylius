<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 10:54
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;


use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class URLRedirect
 *
 * The entity to store URL redirects in Sylius
 *
 * @package Sylius\Component\Core\Model
 */
class URLRedirect implements URLRedirectInterface, ResourceInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $oldRoute;

    /** @var string */
    private $newRoute;

    /** @var bool */
    private $enabled;

    public function __construct(string $oldRoute='', string $newRoute='')
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
}