@viewing_product_in_admin_panel
Feature: Viewing details of a simple product
    In order to view detailed product information
    As an Administrator
    I want to be able to view product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Shield" and "Equipment"
        And the store has a product "Iron Shield" priced at "$20.00"
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Viewing a simple product show page
        When I access the "Iron Shield" product
        Then I should see product show page without variants
        And I should see product name "Iron Shield"
        And I should see product breadcrumb "Iron Shield"

    @ui @no-api
    Scenario: Viewing pricing block
        Given the product "Iron Shield" has original price "$25.00"
        When I access the "Iron Shield" product
        Then I should see price "$20.00" for channel "United States"
        And I should see original price "$25.00" for channel "United States"

    @ui @no-api
    Scenario: Viewing price block without channel enable
        Given this product is unavailable in "United States" channel
        When I access the "Iron Shield" product
        Then I should see product name "Iron Shield"
        And I should see the product in neither channel
        And I should not see price for channel "United States"

    @ui @no-api
    Scenario: Viewing details block
        Given the store has a tax category "No tax" with a code "nt"
        And product's "Iron Shield" code is "123456789"
        And there are 4 units of product "Iron Shield" available in the inventory
        And the product "Iron Shield" belongs to "No tax" tax category
        When I access the "Iron Shield" product
        Then I should see product's code is "123456789"
        And I should see the product is enabled for channel "United States"
        And I should see 4 as a current stock of this product
        And I should see product's tax category is "No tax"

    @ui @no-api
    Scenario: Viewing taxonomy block
        Given this product belongs to "Shield"
        And the product "Iron Shield" has a main taxon "Equipment"
        When I access the "Iron Shield" product
        Then I should see main taxon is "Equipment"
        And I should see product taxon "Shield"

    @ui @no-api
    Scenario: Viewing shipping block
        Given the store has "Over sized" and "Standard" shipping category
        And the product "Iron Shield" has height "10.0", width "15.0", depth "20.0", weight "25.0"
        And this product belongs to "Over sized" shipping category
        When I access the "Iron Shield" product
        Then I should see product's shipping category is "Over sized"
        And I should see product's height is 10
        And I should see product's width is 15
        And I should see product's depth is 20
        And I should see product's weight is 25

    @ui @javascript @api
    Scenario: Viewing media block
        Given the "Iron Shield" product has an image "mugs.jpg" with "main" type
        When I access the "Iron Shield" product
        Then I should see an image related to this product

    @ui @no-api
    Scenario: Viewing "more details" block
        Given the product "Iron Shield" has the slug "iron-shield"
        And the description of product "Iron Shield" is "Shield created by dwarf"
        And the meta keywords of product "Iron Shield" is "shield"
        And the short description of product "Iron Shield" is "good shield"
        When I access the "Iron Shield" product
        Then I should see product name is "Iron Shield"
        And I should see product slug is "iron-shield"
        And I should see product's description is "Shield created by dwarf"
        And I should see product's meta keywords is "shield"
        And I should see product's short description is "good shield"

    @ui @no-api
    Scenario: Viewing associations block
        Given the store has "Similar" and "Dwarf equipment" product association types
        And the store has a "Glass Shield" product
        And the product "Iron Shield" has an association "Similar" with product "Glass Shield"
        When I access the "Iron Shield" product
        Then I should see product association "Similar" with "Glass Shield"
