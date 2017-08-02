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

namespace Sylius\Bundle\ResourceBundle\Form\Builder;

use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DefaultFormBuilderInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function build(MetadataInterface $metadata, FormBuilderInterface $formBuilder, array $options);
}
