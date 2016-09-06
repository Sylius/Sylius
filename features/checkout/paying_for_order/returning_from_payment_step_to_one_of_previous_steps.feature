@checkout
Feature: Returning from payment step to one of previous steps
    In order to modify my order after addressing and selecting shipping method
    As a Customer
    I want to be able to go back to addressing or shipping step from shipping step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Hulk Mug" priced at "$6.99"
        And the store ships everywhere for free
        And the store allows paying with "Paypal Express Checkout"
        And I am a logged in customer

    @ui
    Scenario: Going back to shipping step with button
        Given I have product "Hulk Mug" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the shipping step
        When I decide to change order shipping method
        Then I should be redirected to the shipping step
        And I should be able to go to the payment step again

    @ui
    Scenario: Going back to shipping step with steps panel
        Given I have product "Hulk Mug" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the shipping step
        When I go to the shipping step
        Then I should be redirected to the shipping step
        And I should be able to go to the payment step again

    @ui
    Scenario: Going back to addressing step with steps panel
        Given I have product "Hulk Mug" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the shipping step
        When I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again
