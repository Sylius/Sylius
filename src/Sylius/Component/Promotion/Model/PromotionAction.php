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

namespace Sylius\Component\Promotion\Model;

class PromotionAction implements PromotionActionInterface
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $type;

    /** @var array */
    protected $configuration = [];

    /** @var PromotionInterface|null */
    protected $promotion;

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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        if ($this->type !== $type) {
            $this->configuration = [];
        }

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotion(): ?PromotionInterface
    {
        return $this->promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotion(?PromotionInterface $promotion): void
    {
        $this->promotion = $promotion;
    }
}
