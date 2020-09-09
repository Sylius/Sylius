@managing_product_associations
Feature: Browsing product associations
    In order to see all product associations in the store
    As an Administrator
    I want to browse product associations

    Background:
        Given the store has a product association type "Blue"
        And the store has a product association type "Jean"
        And the store has a product "Blue jean"
        And the store has a product "Grey jean"
        And the store has a product "Blue tee-shirt"
        And the store has a product "Blue jacket"
        And the product "Blue jean" has an association "Blue" with product "Blue tee-shirt"
        And the product "Grey jean" has an association "Blue" with product "Blue jean"
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing product associations in the store
        When I browse product associations
        Then I should see 2 product associations in the list
        And I should see the product association "blue" in the list
