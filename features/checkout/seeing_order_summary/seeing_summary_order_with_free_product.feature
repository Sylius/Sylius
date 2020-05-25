@checkout
Feature: Seeing a summary of the order with free product
    In order to be able to buy free products
    As a Customer
    I want to be able to proceed to the summary page with order containing a free product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Greyjoy Coat" priced at "$0.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Lannister Coat" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And the store has "DHL" shipping method with "$0.00" fee
        And the store allows paying "offline"
        And there is a promotion "All year promotion"
        And it gives "$5.00" discount to every order
        And I am a logged in customer

    @ui
    Scenario: Seeing free order
        Given I have product "Greyjoy Coat" in the cart
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method
        Then I should be on the checkout summary step
        And my order total should be "$0.00"
        And there should be no discount
        And there should be no taxes charged

    @ui
    Scenario: Seeing order with both free and paid products
        Given I have product "Greyjoy Coat" in the cart
        And I have product "Lannister Coat" in the cart
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order total should be "$104.50"
        And my order promotion total should be "-$5.00"
        And my tax total should be "$9.50"
