@modifying_address
Feature: Modifying a customer's shipping address validation
    In order to avoid making mistakes when modifying a customer's shipping address
    As an Administrator
    I want to be prevented from removing required fields from customer's shipping address

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Suit" priced at "$400.00"
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States"
        And the customer set the billing address as "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Address an order without name, city and street
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I clear old shipping address information
        But I do not specify new information
        And I try to save my changes
        Then I should be notified that the "first name", the "last name", the "city" and the "street" in shipping details are required
        And this order should still be shipped to "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
