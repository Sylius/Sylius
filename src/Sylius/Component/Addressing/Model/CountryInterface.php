<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
interface CountryInterface extends ToggleableInterface, ResourceInterface, CodeAwareInterface
{
    /**
     * @return Collection|AdministrativeAreaInterface[]
     */
    public function getAdministrativeAreas();

    /**
     * @param Collection $administrativeArea
     */
    public function setAdministrativeAreas(Collection $administrativeArea);

    /**
     * @return bool
     */
    public function hasAdministrativeAreas();

    /**
     * @param AdministrativeAreaInterface $administrativeArea
     */
    public function addAdministrativeArea(AdministrativeAreaInterface $administrativeArea);

    /**
     * @param AdministrativeAreaInterface $administrativeArea
     */
    public function removeAdministrativeArea(AdministrativeAreaInterface $administrativeArea);

    /**
     * @param AdministrativeAreaInterface $administrativeArea
     *
     * @return bool
     */
    public function hasAdministrativeArea(AdministrativeAreaInterface $administrativeArea);
}
