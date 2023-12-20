@viewing_product_in_admin_panel
Feature: Seeing applied catalog promotions details for a simple product
    In order to be aware of simple product price change reason
    As an Administrator
    I want to see details of catalog promotion nearby product's price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Ursus C-355" priced at "$1,000.00" in "United States" channel
        And there is a catalog promotion with "company_bankruptcy_sale" code and "Company bankruptcy sale" name
        And the catalog promotion "Company bankruptcy sale" is available in "United States"
        And it applies on "Ursus C-355" product
        And it reduces price by "90%"
        And it is enabled
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Seeing applied catalog promotion details on a simple product
        When I access "Ursus C-355" product
        Then this product price should be decreased by catalog promotion "Company bankruptcy sale" in "United States" channel
