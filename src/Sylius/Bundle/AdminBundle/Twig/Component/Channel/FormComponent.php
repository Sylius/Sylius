<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig\Component\Channel;

use Sylius\Bundle\AdminBundle\TwigComponent\HookableComponentTrait;
use Sylius\Component\Core\Model\Channel;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'sylius_admin:channel:form', template: '@SyliusAdmin/channel/form.html.twig')]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Channel $channel = null;

    /** @param class-string $formClass */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->channel);
    }
}
