<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\FieldTypes;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TwigFieldType implements FieldTypeInterface
{
    /**
     * @var DataExtractorInterface
     */
    private $dataExtractor;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param DataExtractorInterface $dataExtractor
     * @param \Twig_Environment $twig
     */
    public function __construct(DataExtractorInterface $dataExtractor, \Twig_Environment $twig)
    {
        $this->dataExtractor = $dataExtractor;
        $this->twig = $twig;
    }

    /**
     * @param Field $field
     * @param $data
     */
    public function render(Field $field, $data)
    {
        if ('.' !== $field->getPath()) {
            $data = $this->dataExtractor->get($field, $data);
        }

        return $this->twig->render($field->getOptions()['template'], ['data' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig';
    }
}
