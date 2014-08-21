<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Twig;

use Sylius\Component\Product\Model\Product;
use Sylius\Component\Promotion\Model\Promotion;

/**
 * Search landing page controller.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class InstanceOfExtension extends \Twig_Extension
{
    /**
     * @var
     */
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getTests()
    {
        $extensionsArray = array();
        foreach ($this->config['orm_indexes'] as $name => $settings) {
            $extensionsArray[$name] = new \Twig_Test_Method($this, 'is'.ucfirst($name));
        }

        return $extensionsArray;
    }

    /**
     * @param $object
     * @return bool
     */
    public function isProduct($object)
    {
        return ($object instanceof Product);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isPromotion($object)
    {
        return ($object instanceof Promotion);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instance_of_extension';
    }

} 