@checkout
Feature: Prevent shipping step completion without a selected shipping method
    In order to prevent finishing the shipping step without a selected method
    As a Customer
    I want to be prevented from completing the shipping step without selecting a shipping method

    Background:
        Given I am a logged in customer

    @api @no-ui
    Scenario: Preventing shipping step completion if there are no available shipping methods
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And I have product "PHP T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I check the details of my cart
        Then I should see that there is no shipment assigned
        And there should not be any shipping method available to choose

    @no-api @ui @javascript
    Scenario: Preventing shipping step completion if there are no available shipping methods
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And I have product "PHP T-Shirt" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I want to complete the shipping step
        Then I should be informed that my order cannot be shipped to this address
        And I should not be able to complete the shipping step

    @api @no-ui
    Scenario: Preventing shipping step completion if there are no available shipping methods for selected country
        Given the store operates on a channel named "Web"
        And the store operates in "United States"
        And the store operates in "France"
        And the store has a product "PHP T-Shirt"
        And the store has a zone "Europe" with code "EU"
        And this zone has the "France" country member
        And the store has "DHL" shipping method with "$20.00" fee within the "EU" zone
        And I have product "PHP T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I try to complete the shipping step
        Then I should see that this shipping method is not available for this address

    @no-api @ui @javascript
    Scenario: Preventing shipping step completion if there are no available shipping methods for selected country
        Given the store operates on a channel named "Web"
        And the store operates in "United States"
        And the store operates in "France"
        And the store has a product "PHP T-Shirt"
        And the store has a zone "Europe" with code "EU"
        And this zone has the "France" country member
        And the store has "DHL" shipping method with "$20.00" fee within the "EU" zone
        And I have product "PHP T-Shirt" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I want to complete the shipping step
        Then I should be informed that my order cannot be shipped to this address
        And I should not be able to complete the shipping step

    @api @no-ui
    Scenario: Preventing shipping step completion if there are no available shipping methods for selected country
        Given the store operates on a channel named "Web"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store operates in "United States"
        And the store operates in "France"
        And the store operates in "Poland"
        And the store has a zone "Europe" with code "EU"
        And this zone has the "France" country member
        And the store has a zone "America" with code "AMR"
        And this zone has the "United States" country member
        And the store has "DHL" shipping method with "$20.00" fee within the "EU" zone
        And the store has "UPS" shipping method with "$20.00" fee within the "AMR" zone
        And I have product "PHP T-Shirt" in the cart
        And I have addressed the cart to "Poland"
        When I try to complete the shipping step with "DHL" shipping method
        Then I should see that this shipping method is not available for this address
        When I try to complete the shipping step with "UPS" shipping method
        Then I should see that this shipping method is also not available for this address
