<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProvinceController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceOrTextFieldFormAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->isHtmlRequest() || null === $countryCode = $request->query->get('countryCode')) {
            throw new AccessDeniedException();
        }

        $addressType = $request->query->get('type');

        /** @var CountryInterface $country */
        if (!$country = $this->get('sylius.repository.country')->findOneBy(['code' => $countryCode])) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasProvinces()) {
            $form = $this->createProvinceTextForm($addressType);

            $view = View::create()
                ->setData([
                    'metadata' => $this->metadata,
                    'form' => $form->createView(),
                ])
                ->setTemplate($configuration->getTemplate('_provinceText.html'))
            ;

            return new JsonResponse([
                'content' => $this->viewHandler->handle($configuration, $view)->getContent(),
            ]);
        }

        $form = $this->createProvinceChoiceForm($country, $addressType);

        $view = View::create()
            ->setData([
                'metadata' => $this->metadata,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate('_provinceChoice.html'))
        ;

        return new JsonResponse([
            'content' => $this->viewHandler->handle($configuration, $view)->getContent(),
        ]);
    }

    /**
     * @param CountryInterface $country
     *
     * @return FormInterface
     */
    protected function createProvinceChoiceForm(CountryInterface $country): FormInterface
    {
        $type= $this->getAddressNameFromType($addressType);
        $fieldname = 'sylius_address_'.$type.'_province';

        return $this->get('form.factory')->createNamed($fieldname, ProvinceCodeChoiceType::class, null, [
            'country' => $country,
            'label' => 'sylius.form.address.province',
            'placeholder' => 'sylius.form.province.select',
        ]);
    }

    /**
     * @param string $addressType
     *
     * @return FormInterface
     */
    protected function createProvinceTextForm(?string $addressType): FormInterface
    {
        $type= $this->getAddressNameFromType($addressType);
        $fieldname = 'sylius_address_'.$type.'_province';

        return $this->get('form.factory')->createNamed($fieldname, TextType::class, null, [
            'required' => false,
            'label' => 'sylius.form.address.province',
        ]);
    }

    /**
    * @param string $addressName
    *
    * @return string
    */
    private function getAddressNameFromType(?string $addressName)
    {
        if ($addressName) {
            preg_match('/sylius-(.*?)-address/', $addressName, $m);
            if (count($m>0)) {
                return  $m[1];
            }
        }
        return '';
    }
}
