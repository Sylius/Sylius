@managing_products
Feature: Adding a new product with text attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with text attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a text product attribute "Gun caliber"
        And the store has a text product attribute "Overall length"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a text attribute to product
        Given I want to create a new simple product
        When I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Gun caliber" attribute to "11 mm" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Gun caliber" of product "44 Magnum" should be "11 mm"

    @ui @javascript
    Scenario: Adding multiple text attributes to product
        Given I want to create a new simple product
        When I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Gun caliber" attribute to "11 mm" in "English (United States)"
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Gun caliber" of product "44 Magnum" should be "11 mm"
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"

    @ui @javascript
    Scenario: Adding and removing text attributes on product create page
        Given I want to create a new simple product
        When I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Gun caliber" attribute to "11 mm" in "English (United States)"
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I remove its "Gun caliber" attribute
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"
        And product "44 Magnum" should not have a "Gun caliber" attribute
