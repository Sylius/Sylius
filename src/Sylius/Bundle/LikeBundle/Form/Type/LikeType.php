<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 17/05/2016
 * Time: 13:05
 */

namespace Sylius\Bundle\LikeBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class LikeType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param string $subject
     */
    public function __construct($dataClass, array $validationGroups = [], $subject)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('authorLike', null, [
                'label' => 'sylius.form.like.author_like',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_like', $this->subject);
    }
}