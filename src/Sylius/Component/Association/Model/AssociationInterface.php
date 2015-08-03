<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Association\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface AssociationInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return AssociationType
     */
    public function getType();

    /**
     * @param AssociationTypeInterface $type
     */
    public function setType(AssociationTypeInterface $type);

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * Returns associated object
     *
     * @return mixed
     */
    public function getAssociatedObject();
}