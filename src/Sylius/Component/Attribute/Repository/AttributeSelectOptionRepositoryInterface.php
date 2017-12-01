<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Sylius\Component\Attribute\Repository;


use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Asier Marqués <asier@simettric.com>
 */
interface AttributeSelectOptionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param AttributeInterface $attribute
     * @return mixed
     */
    public function getAttributeSelectOptionsQB (AttributeInterface $attribute);

}