<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface AttributeInterface extends TimestampableInterface, AttributeTranslationInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @return array
     */
    public function getValidation();

    /**
     * @param array $validation
     */
    public function setValidation(array $validation);

    /**
     * @return AttributeValueInterface[]
     */
    public function getValues();

    /**
     * @return string
     */
    public function getStorageType();

    /**
     * @param string $storageType
     */
    public function setStorageType($storageType);
}
