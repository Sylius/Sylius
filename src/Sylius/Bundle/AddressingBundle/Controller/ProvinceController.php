<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Controller;

use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Webmozart\Assert\Assert;

class ProvinceController extends ResourceController
{
    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceOrTextFieldFormAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->isHtmlRequest() || null === $countryCode = $request->query->get('countryCode')) {
            throw new AccessDeniedException();
        }

        /** @var RepositoryInterface<CountryInterface>|mixed $countryRepository */
        $countryRepository = $this->get('sylius.repository.country');
        Assert::isInstanceOf($countryRepository, RepositoryInterface::class);
        $country = $countryRepository->findOneBy(['code' => $countryCode]);
        if (!$country instanceof CountryInterface) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasProvinces()) {
            $form = $this->createProvinceTextForm();

            $content = $this->renderView(
                $configuration->getTemplate('_provinceText.html'),
                [
                    'metadata' => $this->metadata,
                    'form' => $form->createView(),
                ],
            );

            return new JsonResponse([
                'content' => $content,
            ]);
        }

        $form = $this->createProvinceChoiceForm($country);

        $content = $this->renderView(
            $configuration->getTemplate('_provinceChoice.html'),
            [
                'metadata' => $this->metadata,
                'form' => $form->createView(),
            ],
        );

        return new JsonResponse([
            'content' => $content,
        ]);
    }

    protected function createProvinceChoiceForm(CountryInterface $country): FormInterface
    {
        $formFactory = $this->get('form.factory');
        Assert::isInstanceOf($formFactory, FormFactoryInterface::class);
        return $formFactory->createNamed('sylius_address_province', ProvinceCodeChoiceType::class, null, [
            'country' => $country,
            'label' => 'sylius.form.address.province',
            'placeholder' => 'sylius.form.province.select',
        ]);
    }

    protected function createProvinceTextForm(): FormInterface
    {
        $formFactory = $this->get('form.factory');
        Assert::isInstanceOf($formFactory, FormFactoryInterface::class);
        return $formFactory->createNamed('sylius_address_province', TextType::class, null, [
            'required' => false,
            'label' => 'sylius.form.address.province',
        ]);
    }
}
