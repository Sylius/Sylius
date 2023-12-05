@managing_orders
Feature: Seeing order information about cart promotion in the admin panel locale
    In order to proper information about the promotion
    As an Administrator
    I want to be able to see the cart promotion label in the admin panel locale

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And there is a promotion "Holiday promotion"
        And the promotion has label translation "Promocja wakacyjna" in the "Polish (Poland)" locale
        And the promotion has label translation "Holiday promotion" in the "English (United States)" locale
        And the promotion gives "50%" off on every product when the item total is at least "$10.00"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing a cart promotion label in admin panel locale
        When I view the summary of the order "#00000666"
        Then I should see the cart promotion label "Holiday promotion"

#    @ui
#    Scenario: Switching the locale of admin panel and seeing a cart promotion label in admin panel locale
#        When I change my locale to "Polish (Poland)"
#        And I view the summary of the order "#00000666"
#        Then I should see the cart promotion label "Promocja wakacyjna"
