<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxCategoryInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return Collection|TaxRateInterface[]
     */
    public function getRates();

    /**
     * @param TaxRateInterface $rate
     */
    public function addRate(TaxRateInterface $rate);

    /**
     * @param TaxRateInterface $rate
     */
    public function removeRate(TaxRateInterface $rate);

    /**
     * @param TaxRateInterface $rate
     *
     * @return bool
     */
    public function hasRate(TaxRateInterface $rate);
}
