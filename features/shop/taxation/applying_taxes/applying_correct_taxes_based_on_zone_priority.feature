@applying_taxes
Feature: Apply taxes based on zone priority
    In order to pay correct taxes when a country belongs to multiple zones
    As a Customer
    I want the zone with higher priority to be used for tax calculation

    Background:
        Given the store operates on a single channel
        Given the store operates in "France" and "Belgium"
        And there is a zone "The Rest of the World" containing all other countries
        And the store has a zone "European Union" with code "EU" and priority 2
        And it has the "France" country member
        And it also has the "Belgium" country member
        And default tax zone is "RoW"
        And the store has "No tax" tax rate of 0% for "Clothes" for the rest of the world
        And the store has "VAT" tax rate of 23% for "Clothes" within the "European Union" zone
        And the store has a product "T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And I am a logged in customer

    @api @ui
    Scenario: Applying tax from the higher priority zone
        Given I added product "T-Shirt" to the cart
        And I addressed the cart to "Belgium"
        When I check the details of my cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @api @ui
    Scenario: Applying tax from new zone zone when it has higher priority
        Given the store has a zone "Benelux countries" with code "BC" and priority 3
        And it also has the "Belgium" country member
        And the store has "VAT" tax rate of 0% for "Clothes" within the "Benelux countries" zone
        And I added product "T-Shirt" to the cart
        And I addressed the cart to "Belgium"
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no taxes charged

    @api @ui
    Scenario: Applying tax from current zone when the new one has lower priority
        Given the store has a zone "Benelux countries" with code "GU" and priority 1
        And it also has the "Belgium" country member
        And the store has "VAT" tax rate of 0% for "Clothes" within the "Benelux countries" zone
        And I added product "T-Shirt" to the cart
        And I addressed the cart to "Belgium"
        When I check the details of my cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"
