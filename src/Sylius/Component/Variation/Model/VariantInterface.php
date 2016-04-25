<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface VariantInterface extends TimestampableInterface, ResourceInterface, CodeAwareInterface
{
    /**
     * This should be generated from option values
     * when no other is set.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * @param string $presentation
     */
    public function setPresentation($presentation);

    /**
     * @return VariableInterface
     */
    public function getObject();

    /**
     * @param VariableInterface|null $object
     */
    public function setObject(VariableInterface $object = null);

    /**
     * @return Collection|OptionValueInterface[]
     */
    public function getOptions();

    /**
     * @param Collection $options
     */
    public function setOptions(Collection $options);

    /**
     * @param OptionValueInterface $option
     */
    public function addOption(OptionValueInterface $option);

    /**
     * @param OptionValueInterface $option
     */
    public function removeOption(OptionValueInterface $option);

    /**
     * @param OptionValueInterface $option
     *
     * @return bool
     */
    public function hasOption(OptionValueInterface $option);
}
