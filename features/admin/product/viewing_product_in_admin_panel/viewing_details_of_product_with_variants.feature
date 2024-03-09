@viewing_product_in_admin_panel
Feature: Viewing details of a product with variants
    In order to view detailed product information
    As an Administrator
    I want to be able to view product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over sized" and "Standard" shipping category
        And the store has "Similar" and "dwarf equipment" product association types
        And the store has a "Iron Shield" configurable product
        And the store has a product option "Shield size" with a code "shield_size"
        And the product "Iron Shield" has option "Shield size" named "XL" with code "shield_size_xl"
        And the product "Iron Shield" has option "Shield size" named "XS" with code "shield_size_xs"
        And the product "Iron Shield" has "Iron Shield - very big" variant with code "123456789-xl", price "$25.00", current stock "5"
        And the product "Iron Shield" has "Iron Shield - very small" variant with code "123456789-xs", price "$15.00", current stock "12"
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Viewing a configurable product
        When I access the "Iron Shield" product
        Then I should see product show page with variants
        And I should see product name "Iron Shield"

    @ui @api
    Scenario: Viewing taxonomies
        Given the store classifies its products as "Shield" and "Equipment"
        And the product "Iron Shield" has a main taxon "Equipment"
        And the product "Iron Shield" belongs to taxon "Shield"
        When I access the "Iron Shield" product
        Then I should see main taxon is "Equipment"
        And I should see product taxon "Shield"

    @ui @api
    Scenario: Viewing options
        When I access the "Iron Shield" product
        Then I should see option "Shield size"

    @ui @api
    Scenario: Viewing variants
        When I access the "Iron Shield" product
        Then I should see 2 variants
        And I should see the "Iron Shield - very big" variant
        And I should see the "Iron Shield - very small" variant

    @ui @no-api
    Scenario: Viewing variants' details
        When I access the "Iron Shield" product
        Then I should see 2 variants
        And I should see "Iron Shield - very big" variant with code "123456789-xl", priced "$25.00" and current stock 5 and in "United States" channel
        And I should see "Iron Shield - very small" variant with code "123456789-xs", priced "$15.00" and current stock 12 and in "United States" channel

    @ui @javascript @api
    Scenario: Viewing media
        Given the "Iron Shield" product has an image "mugs.jpg" with "main" type
        When I access the "Iron Shield" product
        Then I should see an image related to this product

    @ui @api
    Scenario: Viewing more details
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

    @ui @api
    Scenario: Viewing association types
        Given the store has a "Glass shield" product
        And the product "Iron Shield" has an association "Similar" with product "Glass shield"
        When I access the "Iron Shield" product
        Then I should see product association type "Similar"

    @ui @no-api
    Scenario: Viewing associations
        Given the store has a "Glass shield" product
        And the product "Iron Shield" has an association "Similar" with product "Glass shield"
        When I access the "Iron Shield" product
        Then I should see product association "Similar" with "Glass shield"
