<?php

declare(strict_types=1);

namespace App\Controller\Action\Shop;

use App\Service\OrderItemReordererInterface; // Мы создадим этот интерфейс и сервис позже
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;
use Symfony\Contracts\Translation\TranslatorInterface;


final class ReorderItemAction
{
    private OrderItemRepositoryInterface $orderItemRepository;
    private OrderRepositoryInterface $orderRepository;
    private OrderItemReordererInterface $orderItemReorderer;
    private FlashBagInterface $flashBag;
    private RouterInterface $router;
    private TranslatorInterface $translator; 

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        OrderItemReordererInterface $orderItemReorderer, // Наш будущий сервис
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemReorderer = $orderItemReorderer;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function __invoke(Request $request, string $orderNumber, int $itemId): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        $flashBag  = $request->getSession()->getFlashBag();

        if (null === $order) {
            throw new NotFoundHttpException('Order not found.');
        }

        // Дополнительная проверка: принадлежит ли заказ текущему пользователю
        // Это обычно делается через event listener или voter в Sylius, но для простоты можно добавить тут
        // $currentUser = $this->security->getUser();
        // if ($order->getCustomer() !== $currentUser) { ... }

        $orderItem = $this->orderItemRepository->find($itemId);

        if (null === $orderItem || $orderItem->getOrder() !== $order) {
            throw new NotFoundHttpException('Order item not found in the specified order.');
        }

        try {
            $this->orderItemReorderer->reorder($orderItem);
            $flashBag->add('success', $this->translator->trans('sylius.ui.item_successfully_added_to_cart')); 
        } catch (\InvalidArgumentException $exception) {
            $this->flashBag->add('error', $exception->getMessage());
        } catch (\Exception $exception) {
            // Общая ошибка
            $flashBag->add('error', $this->translator->trans('sylius.ui.something_went_wrong_adding_item_to_cart'));
        }

        // Получаем URL для редиректа из конфигурации маршрута
        $redirectRoute = $request->attributes->get('_sylius', [])['redirect'] ?? 'sylius_shop_cart_summary';
        if (is_array($redirectRoute)) { // Если редирект с параметрами
            return new RedirectResponse($this->router->generate(key($redirectRoute), current($redirectRoute)));
        }
        return new RedirectResponse($this->router->generate($redirectRoute));
    }
}