<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PaymentMethodInterface extends
    CodeAwareInterface,
    PaymentMethodTranslationInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
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
     * @return string
     */
    public function getInstructions();

    /**
     * @param string $instructions
     */
    public function setInstructions($instructions);

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @param string $environment
     */
    public function setEnvironment($environment);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);
}
