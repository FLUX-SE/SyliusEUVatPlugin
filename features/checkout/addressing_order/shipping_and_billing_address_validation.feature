@checkout_with_vat
Feature: Order addressing validation with VAT field
    In order to avoid making mistakes when addressing an order
    As an Customer
    I want to be prevented from adding a false VAT number

    Background:
        And the store operates on a single channel
        And the store operates in "France"
        And the store has a zone "European Union" with code "EU"
        And this zone has the "France" country member
        And this channel is based in the "France" country and allow VAT numbers for the "EU" zone
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am a logged in customer
        And I have product "PHP T-Shirt" in the cart

    @api @ui @javascript
    Scenario: Address an order with a country different from the VAT country number
        Given I am at the checkout addressing step
        When I specify the billing address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
        And I specify the billing vat number as "ES01234567891"
        And I specify the shipping address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
        And I specify the shipping vat number as "ES01234567891"
        And I try to complete the addressing step
        Then I should be notified that the VAT number in shipping details is not corresponding with the selected country
        And I should be notified that the VAT number in billing details is not corresponding with the selected country

    @api @ui @javascript
    Scenario: Address an order with a country equal to the VAT country number
        Given I am at the checkout addressing step
        When I specify the billing address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
        And I specify the billing vat number as "FR01234567891"
        And I specify the shipping address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
        And I specify the shipping vat number as "FR01234567891"
        And I try to complete the addressing step
        Then I should be notified that the VAT number in shipping details is not valid
        And I should be notified that the VAT number in billing details is not valid

    @api @ui @javascript
    Scenario: Address an order with a country equal to the VAT country number
        Given the store operates in "United States"
        And I am at the checkout addressing step
        When I specify the billing address as "Gotham", "Mountain Drive", "1007", "United States" for "Bruce Wayne"
        And I try to specify the billing vat number as "99-0999999"
        And I specify the shipping address as "Gotham", "Mountain Drive", "1007", "United States" for "Bruce Wayne"
        And I try to specify the shipping vat number as "99-0999999"
        Then I should not be able to specify VAT number manually for shipping address
        And I should not be able to specify VAT number manually for billing address
