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

use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class CatalogPromotion implements CatalogPromotionInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    protected ?string $name = null;

    protected ?string $code = null;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): ?string
    {
        return $this->getTranslation()->getLabel();
    }

    public function setLabel(?string $label): void
    {
        $this->getTranslation()->setLabel($label);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /** @return CatalogPromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var CatalogPromotionTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): CatalogPromotionTranslationInterface
    {
        return new CatalogPromotionTranslation();
    }
}
