<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use Sylius\Component\Translation\Factory\TranslatableFactory;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SyliusResource extends AbstractSyliusResource
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var SyliusTranslationResource
     */
    private $translationResource;

    /**
     * @param string $name
     * @param string|null $modelClass
     * @param string|null $interfaceClass
     */
    public function __construct($name, $modelClass = null, $interfaceClass = null)
    {
        parent::__construct($modelClass, $interfaceClass);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function useDefaultTranslatableFactory()
    {
        $this->useFactory(TranslatableFactory::class);

        return $this;
    }

    /**
     * @return SyliusTranslationResource|null
     */
    public function getTranslationResource()
    {
        return $this->translationResource;
    }

    /**
     * @param SyliusTranslationResource $translationResource
     *
     * @return $this
     */
    public function useTranslationResource(SyliusTranslationResource $translationResource)
    {
        $this->translationResource = $translationResource;

        return $this;
    }
}
