@managing_products
Feature: Adding a new product with text attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with text attribute to the shop

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And the store has a text product attribute "Gun caliber"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a text attribute to product
        Given I want to create a new simple product
        When I specify its code as "44_MAGNUM"
        And I name it ".44 Magnum" in "English (United States)"
        And I set its price to "$100.00"
        And I set its "Gun caliber" attribute to "11 mm"
        And I add it
        Then I should be notified that it has been successfully created
        And the product ".44 Magnum" should appear in the shop
        And attribute "Gun caliber" of product ".44 Magnum" should be "11 mm"
