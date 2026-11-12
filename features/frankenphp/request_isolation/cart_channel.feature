@worker @worker:cart @mink:workerphp
Feature: Cart isolation between channels in consecutive requests
    In order to shop on several storefronts
    As a Customer
    I want a cart I filled on one channel not to follow me to another

    Carts belong to a channel. The cart context is resolved per request, so in
    worker mode a context that remembers the first channel's cart serves it to
    the next request - on the other channel. Both visits live in ONE scenario,
    because the leak needs a predecessor.

    Background:
        #  Poland last: the setup steps act on whichever channel was created
        #  last, and the cart has to land there.
        Given the store operates on a channel named "United States"
        And the store has a product "Banana" priced at "$5.00" in "United States" channel
        And the store operates on another channel named "Poland"
        And the store has a product "Onion" priced at "$10.00" in "Poland" channel
        And there is a customer account "robb@stark.com" identified by "KingInTheNorth"

    @no-api @ui
    Scenario: A cart filled on one channel is not served on the other
        Given I am logged in as "robb@stark.com"
        And I added product "Onion" to the cart
        When I browse the "Poland" channel
        And I check the details of my cart
        Then my cart total should be "$10.00"
        When I browse the "United States" channel
        And I check the details of my cart
        Then my cart should be empty
