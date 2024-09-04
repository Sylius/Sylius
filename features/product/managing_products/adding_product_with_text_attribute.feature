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

    @ui @javascript @no-api
    Scenario: Adding a text attribute to product
        When I want to create a new simple product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Gun caliber" attribute to "11 mm" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Gun caliber" of product "44 Magnum" should be "11 mm"

    @ui @javascript @no-api
    Scenario: Adding a non-translatable text attribute to product
        When I want to create a new simple product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its non-translatable "Author" attribute to "Colt"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And non-translatable attribute "Author" of product "44 Magnum" should be "Colt"

    @ui @javascript @no-api
    Scenario: Adding and removing text attributes on product create page
        When I want to create a new simple product
        And I specify its code as "44_MAGNUM"
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
