@managing_products
Feature: Adding a new product with text attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with text attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a text product attribute "Gun caliber"
        And the store has a text product attribute "Overall length"
        And the store has a non-translatable text product attribute "Author"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a text attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I add the "Gun caliber" attribute
        And I set the "Gun caliber" attribute value to "11 mm" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Gun caliber" of product "44 Magnum" should be "11 mm"

    @todo @ui @mink:chromedriver @api
    Scenario: Adding a non-translatable text attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its non-translatable "Author" attribute to "Colt"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And non-translatable attribute "Author" of product "44 Magnum" should be "Colt"

    @ui @mink:chromedriver @api
    Scenario: Adding and removing text attributes on product create page
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I add the "Gun caliber" attribute
        And I set the "Gun caliber" attribute value to "11 mm" in "English (United States)"
        And I add the "Overall length" attribute
        And I set the "Overall length" attribute value to "30.5 cm" in "English (United States)"
        And I remove its "Gun caliber" attribute
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Overall length" of product "44 Magnum" should be "30.5 cm"
        And product "44 Magnum" should not have a "Gun caliber" attribute

    @api @no-ui
    Scenario: Trying to add an invalid text attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set the invalid integer value of the non-translatable "Author" attribute to 5
        And I try to add it
        Then I should be notified that the value of the "Author" attribute has invalid type
        And product with code "44_MAGNUM" should not be added
