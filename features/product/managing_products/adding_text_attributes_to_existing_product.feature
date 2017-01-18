@managing_products
Feature: Adding attributes to an existing product
    In order to extend product details
    As an Administrator
    I want to be able to add attributes to an existing product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "44 Magnum"
        And the store has a text product attribute "Overall length"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a text attribute to an existing product
        When I want to modify the "44 Magnum" product
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"

    @ui @javascript
    Scenario: Adding another text attribute to an existing product
        Given this product has text attribute "Gun caliber" with value "11 mm" in "English (United States)" locale
        When I want to modify the "44 Magnum" product
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And attribute "Gun caliber" of product "44 Magnum" should be "11 mm"
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"

    @ui @javascript
    Scenario: Adding and removing text attributes on product update page
        When I want to modify the "44 Magnum" product
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I remove its "Overall length" attribute
        And I save my changes
        Then I should be notified that it has been successfully edited
        And product "44 Magnum" should not have a "Overall length" attribute

    @ui @javascript
    Scenario: Adding and removing after saving text attributes on product update page
        Given this product has text attribute "Gun caliber" with value "11 mm" in "English (United States)" locale
        When I want to modify the "44 Magnum" product
        And I set its "Overall length" attribute to "30.5 cm" in "English (United States)"
        And I save my changes
        And I remove its "Gun caliber" attribute
        And I save my changes
        Then I should be notified that it has been successfully edited
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"
        And product "44 Magnum" should not have a "Gun caliber" attribute
