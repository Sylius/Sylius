@checkout
Feature: Having a default address preselected
    In order to speed up checkout process
    As a Customer
    I want to have my default address preselected on addressing step
    
    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And my default address is of "Lucifer Morningstar"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free

    @ui
    Scenario: Having a default address preselected on checkout addressing step
        When I add product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        Then address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be filled as shipping address

    @ui
    Scenario: Not modifying a default address in address book after modifying it on addressing step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I am browsing my address book
        Then address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should still be marked as my default address
