@checkout
Feature: Seeing order addresses on order summary page
    In order be certain about shipping and bulling address
    As a Customer
    I want to be able to see addresses on order summary page

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am logged in customer

    @todo
    Scenario: Seeing the same shipping and billing address on order summary
        Given I have product "Lannister Coat" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I complete order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary page
        And I should see this shipping address as shipping and billing address

    @todo
    Scenario: Seeing different shipping and billing address on order summary
        Given I have product "Lannister Coat" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I choose the different billing address
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Eddard Stark"
        And I complete order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary page
        And I should see this shipping address as shipping address
        And I should see this billing address as billing address
