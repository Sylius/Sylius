@modifying_shipping_address
Feature: Modifying a customer's shipping address validation
    In order to avoid making mistakes when modifying a customer's shipping address
    As an Administrator
    I want to be prevented from removing required fields from customer's shipping address

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And that channel uses the "USD" currency by default
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Suit" priced at "$400.00"
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Trying to modify a customer's shipping address without specifying a first name and a last name
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify the street as "Seaside Fwy"
        And I choose "United States" as the country
        And I specify the city as "Los Angeles"
        And I specify the postcode as "90802"
        But I do not specify the first name
        And I do not specify the last name
        And I try to save my changes
        Then I should be notified that the first name is required
        And I should be notified that the last name is required
        And this order should still be shipped to "Mike Ross", "350 5th Ave", "10118", "New York", "United States"

    @ui
    Scenario: Trying to modify a customer's shipping address without specifying a city and a street
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify the first name as "Lucifer"
        And I specify the last name as "Morningstar"
        And I choose "United States" as the country
        And I specify the postcode as "90802"
        And I do not specify the street
        And I do not specify the city
        And I try to save my changes
        Then I should be notified that the street is required
        And I should be notified that the city is required
        And this order should still be shipped to "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
