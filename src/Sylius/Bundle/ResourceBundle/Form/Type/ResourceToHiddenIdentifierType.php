<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceToHiddenIdentifierType extends ResourceToIdentifierType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s_to_hidden_identifier', $this->metadata->getApplicationName(), $this->metadata->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return sprintf('%s_%s_to_hidden_identifier', $this->metadata->getApplicationName(), $this->metadata->getName());
    }
}
