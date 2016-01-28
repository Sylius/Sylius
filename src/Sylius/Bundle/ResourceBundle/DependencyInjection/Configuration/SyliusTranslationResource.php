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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SyliusTranslationResource extends AbstractSyliusResource
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @return array
     */
    public function getTranslatableFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setTranslatableFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
