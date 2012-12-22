<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

/**
 * Tax category interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface TaxCategoryInterface
{
    public function getId();
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
    public function getRates();
    public function addRate(TaxRateInterface $rate);
    public function removeRate(TaxRateInterface $rate);
    public function hasRate(TaxRateInterface $rate);
    public function getCreatedAt();
    public function getUpdatedAt();
}
