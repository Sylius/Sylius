@checkout
Feature: Changing address during checkout
    In order to place an order with the correct address
    As a Visitor
    I want to be able to change the address during checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And the store ships everywhere for Free

    @ui @no-api
    Scenario: Going back to addressing step with and changing email
        Given I have product "T-Shirt banana" in the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I decide to change my address
        And I specify the email as "ned.stark@example.com"
        And I complete the addressing step
        Then I should be checking out as "ned.stark@example.com"

    @api
    Scenario: Changing address
        Given the visitor has product "T-Shirt banana" in the cart
        And the visitor has specified the email as "jon.snow@example.com"
        And the visitor has specified address as "Los Angeles", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor has completed the addressing step
        When the visitor changes the billing address to "Los Angeles", "Avenue", "90210", "United States" for "Jon Snow"
        And the visitor completes the addressing step
        Then the visitor should has "Los Angeles", "Avenue", "90210", "United States", "Jon Snow" specified as billing address
        Then the visitor should has "Los Angeles", "Avenue", "90210", "United States", "Jon Snow" specified as shipping address
