[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

# Sylius Plugin for VIES Client

## Installation

```$bash
composer require prometee/sylius-vies-client-plugin
```
## Configuration

Add config to add required `vat_number` field to `Customer`

```$yaml
# config/packages/prometee_sylius_vies_client.yaml

imports:
    - { resource: "@PrometeeSyliusVIESClientPlugin/Resources/config/config.yaml" }

```

Update your database :

```$bash
php ./bin/console doctrine:migrations:diff
php ./bin/console doctrine:migrations:migrate
```

Add required configs :

```$yaml
# config/packages/prometee_sylius_vies_client.yaml

imports:
    - { resource: "@PrometeeSyliusVIESClientPlugin/Resources/config/config.yaml" }
```

Load some required fixtures :

```$bash
./bin/console sylius:fixture:load european_vat_zones
````

Go to your admin panel and edit your `Channel` to set the two fields to indicate to this plugin :
1. What is your base country to compare to your customer country.
2. What is the European zone to know if the customer is part of the Europe or not.

[ico-version]: https://img.shields.io/packagist/v/Prometee/sylius-vies-client-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Prometee/SyliusVIESClientPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Prometee/SyliusVIESClientPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/prometee/sylius-vies-client-plugin
[link-travis]: https://travis-ci.org/Prometee/SyliusVIESClientPlugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin
