@managing_products
Feature: Adding a new product with an integer attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with an integer attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has an integer product attribute "Production year"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding an integer attribute to product
        Given I want to create a new simple product
        When I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Production year" attribute to "1955" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Production year" of product "44 Magnum" should be "1955"
