@checkout
Feature: Returning to addressing step with a different shipping address
    In order to change the shipping address
    As a Visitor
    I want to return to the addressing step and change the shipping address

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Summer T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free

    @no-api @ui @mink:chromedriver
    Scenario: Going back to addressing step after submitting a different shipping address
        When I add product "Summer T-Shirt" to the cart
        And I go to the checkout addressing step
        And I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I specify the shipping address as "Brooklyn", "70 Joy Ridge St", "11225", "United States" for "Jane Doe"
        And I complete the addressing step
        And I decide to change my address
        Then different shipping address should be checked

    @no-api @ui @javascript
    Scenario: Going back to addressing step after not submitting a different shipping address
        When I add product "Summer T-Shirt" to the cart
        And I go to the checkout addressing step
        And I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I complete the addressing step
        And I decide to change my address
        Then different shipping address should not be checked

    @no-api @ui @mink:chromedriver
    Scenario: Going back to addressing step after submitting a different shipping address
        When I add product "Summer T-Shirt" to the cart
        And I go to the checkout addressing step
        And I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I specify the shipping address as "Brooklyn", "70 Joy Ridge St", "11225", "United States" for "Jane Doe"
        And I complete the addressing step
        And I decide to change my address
        And shipping address should be visible

    @no-api @ui @javascript
    Scenario: Going back to addressing step after not submitting a different shipping address
        When I add product "Summer T-Shirt" to the cart
        And I go to the checkout addressing step
        And I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I complete the addressing step
        And I decide to change my address
        And shipping address should not be visible
