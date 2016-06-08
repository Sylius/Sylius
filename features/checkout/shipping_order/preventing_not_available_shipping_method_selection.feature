@checkout_shipping
Feature: Preventing not available shipping method selection
    In order to ship my order properly
    As a Customer
    I want to not be able to choose not available shipping methods

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Targaryen T-Shirt" priced at "$19.99"
        And I am logged in customer

    @ui
    Scenario: Not being able to select disabled shipping method
        Given the store has "Raven Post" shipping method with "€10.00" fee
        And the store has disabled "Dragon Post" shipping method with "€30.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I proceed with the checkout addressing step
        And I specify the shipping address
        And I proceed with the next step
        Then I should not be able to select "Dragon Post" shipping method

    @ui
    Scenario: Not being able to select shipping method not available for my shipping address
        Given there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And the store has "Dragon Post" shipping method with "€30.00" fee within "EU" zone
        And the store has "Raven Post" shipping method with "€10.00" fee for the rest of the world
        And I have product "Targaryen T-Shirt" in the cart
        When I proceed with the checkout addressing step
        And I specify the shipping address
        And I proceed with the next step
        Then I should not be able to select "Raven Post" shipping method

    @ui
    Scenario: Being alerted about no shipping method available
        Given there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And the store has "Dragon Post" shipping method with "€30.00" fee for the rest of the world
        And the store has disabled "Raven Post" shipping method with "€10.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I proceed with the checkout addressing step
        And I specify the shipping address
        And I proceed with the next step
        Then I should not be able to select "Raven Post" shipping method
        And I should not be able to select "Dragon Post" shipping method
        And I should be alerted, that there is no shipping method available for me
