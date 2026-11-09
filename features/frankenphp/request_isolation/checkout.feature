@worker @worker:checkout @mink:workerphp
Feature: Channel isolation after a completed checkout
    In order to keep serving every channel correctly
    As a Developer
    I want a checkout on one channel not to bleed into the next request on another

    A checkout is the longest sequence of requests a shopper makes, and the one
    that touches the most shared services - cart, addressing, shipping, payment,
    order. In worker mode all of it happens in one process, so whatever it
    leaves behind is still there for the request that follows. The assertion is
    therefore AFTER the order is placed, on a different channel.

    Background:
        #  Order matters twice over. The setup steps act on whichever channel
        #  was created LAST, so the one the checkout runs on has to come last.
        #  And it has to be the "in the <country>" variant, because that is what
        #  creates the zone the shipping method belongs to - without a zone the
        #  shipping step offers nothing to pick.
        Given the store operates on a channel named "Poland"
        And the store has a product "Onion" priced at "$10.00" in "Poland" channel
        And the store operates on a single channel in the "United States" named "United States"
        And the store has a product "Banana" priced at "$5.00" in "United States" channel
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a user "robb@stark.com" identified by "KingInTheNorth"

    @no-api @ui
    Scenario: The other channel still sees only its own products after an order was placed
        Given I am logged in as "robb@stark.com"
        And I added product "Banana" to the cart
        And I addressed the cart
        #  Establishes the _channel_code cookie. These channels have no
        #  hostname, so without it the worker cannot resolve a channel for a
        #  request to 127.0.0.1 and the page dies with "Channel could not be
        #  found!".
        When I browse the "United States" channel
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        And I confirm my order
        Then my order should be completed successfully
        When I browse the "Poland" channel
        Then I should see "Onion" product
        But I should not see "Banana" product

    @no-api @ui
    Scenario: The customer can view their order in account history after checkout
        Given I am logged in as "robb@stark.com"
        And I added product "Banana" to the cart
        And I addressed the cart
        When I browse the "United States" channel
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        And I confirm my order
        Then my order should be completed successfully
        When I browse my orders
        Then I should see a single order in the list

    @no-api @ui
    Scenario: An anonymous visitor on the same channel after an order was placed does not inherit the previous order
        Given I am logged in as "robb@stark.com"
        And I added product "Banana" to the cart
        And I addressed the cart
        When I browse the "United States" channel
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        And I confirm my order
        Then my order should be completed successfully
        When I log out
        And I try to browse my orders
        Then I should be denied an access to order list
        When I browse the "United States" channel
        Then I should see "Banana" product

    @no-api @ui
    Scenario: Orders placed by consecutive customers do not leak between their accounts
        Given there is a user "sansa@stark.com" identified by "queenInTheNorth"
        And I am logged in as "robb@stark.com"
        And I added product "Banana" to the cart
        And I addressed the cart
        When I browse the "United States" channel
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        And I confirm my order
        Then my order should be completed successfully
        When I browse my orders
        Then I should see a single order in the list
        When I log out
        And I try to browse my orders
        Then I should be denied an access to order list
        When I am logged in as "sansa@stark.com"
        And I added product "Banana" to the cart
        And I addressed the cart
        When I browse the "United States" channel
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        And I confirm my order
        Then my order should be completed successfully
        When I browse my orders
        Then I should see a single order in the list
        When I log out
        And I am logged in as "robb@stark.com"
        And I browse my orders
        Then I should see a single order in the list
