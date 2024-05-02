@checkout
Feature: Returning to addressing step with a different shipping address
    In order to change the shipping address
    As a Visitor
    I want to return to the addressing step and change the shipping address

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Summer T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free

    @ui @no-api
    Scenario: Going back to addressing step after submitting a different shipping address
        Given I have product "Summer T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I specify the shipping address as "Brooklyn", "70 Joy Ridge St", "11225", "United States" for "Jane Doe"
        And I complete the addressing step
        And I decide to change my address
        Then different shipping address should be checked

    @ui @no-api
    Scenario: Going back to addressing step after not submitting a different shipping address
        Given I have product "Summer T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I complete the addressing step
        And I decide to change my address
        Then different shipping address should not be checked

    @ui @no-api @mink:chromedriver
    Scenario: Going back to addressing step after submitting a different shipping address
        Given I have product "Summer T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I specify the shipping address as "Brooklyn", "70 Joy Ridge St", "11225", "United States" for "Jane Doe"
        And I complete the addressing step
        And I decide to change my address
        And shipping address should be visible

    @ui @no-api @mink:chromedriver
    Scenario: Going back to addressing step after not submitting a different shipping address
        Given I have product "Summer T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "john.doe@example.com"
        And I specify the billing address as "Brooklyn", "9036 Country Club Ave.", "11230", "United States" for "John Doe"
        And I complete the addressing step
        And I decide to change my address
        And shipping address should not be visible
