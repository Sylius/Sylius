<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TranslatableResourceRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $translatableFields
     *
     * @return self
     */
    public function setTranslatableFields(array $translatableFields);
}
