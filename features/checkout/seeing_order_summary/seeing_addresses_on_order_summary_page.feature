@checkout
Feature: Seeing order addresses on order summary page
    In order to be certain about shipping and billing address
    As a Customer
    I want to be able to see addresses on the order summary page

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing the same shipping and billing address on order summary
        Given I have product "Lannister Coat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I complete order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And I should see this shipping address as shipping and billing address

    @ui
    Scenario: Seeing different shipping and billing address on order summary
        Given I have product "Lannister Coat" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I choose the different billing address
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Eddard Stark"
        And I complete the addressing step
        And I complete order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And I should see this shipping address as shipping address
        And I should see this billing address as billing address
