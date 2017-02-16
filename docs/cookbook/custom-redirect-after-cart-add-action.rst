How to change a redirect after the add to cart action?
======================================================

Currently **Sylius** by default is using route definition and **sylus-add-to-cart.js** script to handle redirect after successful add to cart action.

.. code-block:: yaml

    sylius_shop_partial_cart_add_item:
        path: /add-item
        methods: [GET]
        defaults:
            _controller: sylius.controller.order_item:addAction
            _sylius:
                template: $template
                factory:
                    method: createForProduct
                    arguments: [expr:service('sylius.repository.product').find($productId)]
                form:
                    type: Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType
                    options:
                        product: expr:service('sylius.repository.product').find($productId)
                redirect:
                    route: sylius_shop_cart_summary
                    parameters: {}

.. code-block:: javascript

    $.fn.extend({
        addToCart: function () {
            var element = $(this);
            var href = $(element).attr('action');
            var redirectUrl = $(element).data('redirect');
            var validationElement = $('#sylius-cart-validation-error');

            $(element).api({
                method: 'POST',
                on: 'submit',
                cache: false,
                url: href,
                beforeSend: function (settings) {
                    settings.data = $(this).serialize();

                    return settings;
                },
                onSuccess: function (response) {
                    validationElement.addClass('hidden');
                    window.location.replace(redirectUrl);
                },
                onFailure: function (response) {
                    validationElement.removeClass('hidden');
                    var validationMessage = '';

                    $.each(response.errors.errors, function (key, message) {
                        validationMessage += message;
                    });
                    validationElement.html(validationMessage);
                    $(element).removeClass('loading');
                },
            });
        }
    });

If you want to have custom logic after cart add action you can use **ResourceControllerEvent** to set your custom response.

Let's assume that you would like such a feature in your system:

.. code-block:: php

    <?php

    final class ChangeRedirectAfterAddingToCartListener
    {
        /**
         * @var RouterInterface
         */
        private $router;

        /**
         * @param RouterInterface $router
         */
        public function __construct(RouterInterface $router)
        {
            $this->router = $router;
        }

        /**
         * @param ResourceControllerEvent $event
         */
        public function onSuccessfulAddToCart(ResourceControllerEvent $event)
        {
            if (!$event->getSubject() instanceof OrderItemInterface) {
                throw new \LogicException(
                    sprintf('This listener operates only on order item, got "$s"', get_class($event->getSubject()))
                );
            }

            $newUrl = $this->router->generate('your_new_route_name', []);

            $event->setResponse(new RedirectResponse($newUrl));
        }
    }

.. code-block:: xml

    <service id="sylius.listener.change_redirect_after_adding_to_cart" class="Sylius\Bundle\ShopBundle\EventListener\ChangeRedirectAfterAddingToCartListener">
        <argument type="service" id="router" />
        <tag name="kernel.event_listener" event="sylius.order_item.post_add" method="onSuccessfulAddToCart" />
    </service>

Next thing to do is handling it by your frontend application.
