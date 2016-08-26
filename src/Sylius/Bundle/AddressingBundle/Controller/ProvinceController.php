<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProvinceController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceFormAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        if (!$configuration->isHtmlRequest() || null === $countryCode = $request->query->get('countryCode')) {
            throw new AccessDeniedException();
        }

        /* @var CountryInterface $country */
        if (!$country = $this->get('sylius.repository.country')->findOneBy(['code' => $countryCode])) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasProvinces()) {
            return new JsonResponse(['content' => false]);
        }

        $form = $this->createProvinceChoiceForm($country);

        $view = View::create()
            ->setData([
                'metadata' => $this->metadata,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate('_provinceChoiceForm.html'))
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
    protected function createProvinceChoiceForm(CountryInterface $country)
    {
        return $this->get('form.factory')->createNamed('sylius_address_province', 'sylius_province_code_choice', null, [
            'country' => $country,
            'label' => 'sylius.form.address.province',
            'empty_value' => 'sylius.form.province.select',
        ]);
    }
}
