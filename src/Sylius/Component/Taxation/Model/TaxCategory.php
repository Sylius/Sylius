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

namespace Sylius\Component\Taxation\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

class TaxCategory implements TaxCategoryInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Collection|TaxRateInterface[]
     */
    protected $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    /**
     * {@inheritdoc}
     */
    public function addRate(TaxRateInterface $rate): void
    {
        if (!$this->hasRate($rate)) {
            $rate->setCategory($this);
            $this->rates->add($rate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRate(TaxRateInterface $rate): void
    {
        if ($this->hasRate($rate)) {
            $rate->setCategory(null);
            $this->rates->removeElement($rate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasRate(TaxRateInterface $rate): bool
    {
        return $this->rates->contains($rate);
    }
}
