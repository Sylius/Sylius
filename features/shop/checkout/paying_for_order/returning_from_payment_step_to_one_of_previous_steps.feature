@checkout
Feature: Returning from payment step to one of previous steps
    In order to modify my order after addressing and selecting shipping method
    As a Visitor
    I want to be able to go back to addressing or shipping step from shipping step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Hulk Mug" priced at "$6.99"
        And the store ships everywhere for Free
        And the store allows paying with "Bank transfer"

    @no-api @ui
    Scenario: Going back to shipping step with button
        Given I added product "Hulk Mug" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I complete the shipping step
        And I decide to change shipping method
        Then I should be redirected to the shipping step
        And I should be able to go to the payment step again

    @no-api @ui
    Scenario: Going back to shipping step with steps panel
        Given I added product "Hulk Mug" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I complete the shipping step
        And I go to the shipping step
        Then I should be redirected to the shipping step
        And I should be able to go to the payment step again

    @no-api @ui
    Scenario: Going back to addressing step with steps panel
        Given I added product "Hulk Mug" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I complete the shipping step
        And I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again
