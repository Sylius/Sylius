@checkout
Feature: Choosing an address from address book
    In order to address an order by choosing it from my address book
    As a Customer
    In order to quickly fill in my address information during checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store also has country "Australia"
        And this country has the "Queensland" province with "AU-QLD" code
        And this country also has the "Victoria" province with "AU-VIC" code
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And I have an address "Fletcher Ren", "Upper Barkly Street", "3377", "Ararat", "Australia", "Victoria" in my address book

    @ui @javascript
    Scenario: Choosing shipping address from address book
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Seaside Fwy" street for shipping address
        Then address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be filled as shipping address

    @ui @javascript
    Scenario: Choosing billing address from address book
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Seaside Fwy" street for billing address
        Then address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be filled as billing address

    @ui @javascript
    Scenario: Choosing shipping address which contains a country with provinces from my address book
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Upper Barkly Street" street for shipping address
        Then address "Fletcher Ren", "Upper Barkly Street", "3377", "Ararat", "Australia", "Victoria" should be filled as shipping address

    @ui @javascript
    Scenario: Choosing shipping address from address book and proceed to the next step
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Seaside Fwy" street for shipping address
        And I complete the addressing step
        Then I should be on the checkout shipping step
