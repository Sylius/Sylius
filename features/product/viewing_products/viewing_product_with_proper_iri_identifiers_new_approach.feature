@viewing_products
Feature: Viewing a product with proper iri identifiers
    In order to see products with proper iri identifiers
    As a Visitor
    I want to be able to view a single product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Lamborghini Gallardo Model" configurable product
        And this product has "Red", "Yellow" and "Black" variants
        And this product has option "Engine Power" with values "500KM", "750KM" and "1000KM"
        And the store classifies its products as "Super Car"
        And this product is in "Super Car" taxon at 1st position
        And the product "Lamborghini Gallardo Model" has a main taxon "Super Car"
        And this product has an image "lamborghini.jpg" with "main" type
        And this product has a review titled "Great Car" and rated 5 added by customer "h.p.lovecraft@arkham.com", created 3 days ago

    @api
    Scenario: Viewing proper iri identifiers for visitor
        When I check this product's details
        Then this product should have only shop iri's identifiers
