@checkout_addressing
Feature: Order addressing validation
    In order to avoid making mistakes when addressing an order
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am logged in customer

    @ui
    Scenario: Address an order without name
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I do not specify the first name
        And I do not specify the last name
        And I specify the street as "100 MAIN ST"
        And I specify the city as "New York City"
        And I specify the postcode as "93-554"
        And I try to proceed with the next step
        Then I should be notified that the "shipping" "first name" is required
        And the "shipping" "last name" is also required

    @ui
    Scenario: Address an order without the street
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I specify the first name as "Jon"
        And I specify the last name as "Snow"
        And I do not specify the street
        And I choose "France"
        And I specify the city as "Ankh Morpork"
        And I specify the postcode as "90210"
        And I try to proceed with the next step
        Then I should be notified that the "shipping" "street" is required

    @ui
    Scenario: Address an order without the city and the postcode
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I specify the first name as "Jon"
        And I specify the last name as "Snow"
        And I specify the street as "100 MAIN ST"
        And I choose "France"
        And I do not specify the city
        And I do not specify the postcode
        And I try to proceed with the next step
        Then I should be notified that the "shipping" "city" is required
        And the "shipping" "postcode" is also required

    @ui
    Scenario: Address an order's billing address without name
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I specify the shipping address
        And I choose the different billing address
        And I do not specify the billing's first name
        And I do not specify the billing's last name
        And I specify the billing's street as "100 MAIN ST"
        And I specify the billing's city as "New York City"
        And I specify the billing's postcode as "93-554"
        And I try to proceed with the next step
        Then I should be notified that the "billing" "first name" is required
        And the "billing" "last name" is also required

    @ui
    Scenario: Address an order's billing address without the street
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I specify the shipping address
        And I choose the different billing address
        And I specify the billing's first name as "John"
        And I specify the billing's last name as "Doe"
        And I do not specify the billing's street
        And I specify the billing's city as "New York City"
        And I specify the billing's postcode as "93-554"
        And I try to proceed with the next step
        Then I should be notified that the "billing" "street" is required

    @ui
    Scenario: Address an order's billing address without the city and the postcode
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        Then I specify the shipping address
        And I choose the different billing address
        And I specify the billing's first name as "John"
        And I specify the billing's last name as "Doe"
        And I specify the billing's street as "100 MAIN ST"
        And I do not specify the billing's city
        And I do not specify the billing's postcode
        And I try to proceed with the next step
        Then I should be notified that the "billing" "city" is required
        And the "billing" "postcode" is also required
