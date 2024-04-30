# UPGRADE FROM `v1.2.0` TO `v2.0.0`

* **BC BREAK**: new bundle folder structure : `src/Resources/*` has been moved to the top level
  * `src/Resources/config` -> `config`
  * `src/Resources/views` -> `templates`
  * `src/Resources/translations` -> `translations`
* **BC BREAK**: Rename applicator service ID from
  `flux_se.sylius_eu_vat_plugin.applicator.order_european_vatnumber_applicator` to
  `flux_se.sylius_eu_vat_plugin.applicator.order_european_vat_number`
* **BC BREAK**: Part of the class `OrderEuropeanVATNumberApplicator` has been extracted to
  `src/Extractor` and `src/Checker` services.
* **BC BREAK**: Rename applicator service ID from
  `flux_se.sylius_eu_vat_plugin.form.extension.vatnumber` to
  `flux_se.sylius_eu_vat_plugin.form.extension.vat_number`

# UPGRADE FROM `v1.0.8` TO `v1.1.0`

* Execute this sql query to avoid the `Migration` script to be re-added :

```mysql
UPDATE sylius_migrations SET version="FluxSE\\SyliusEUVatPlugin\\Migrations\\Version20190507174705"
    WHERE version="Prometee\\SyliusVIESClientPlugin\\Migrations\\Version20190507174705";
```

* **BC BREAK**: Rename composer vendor name from
  `prometee\sylius-vies-client-plugin` to `flux-se\sylius-eu-vat-plugin`
* **BC BREAK**: Rename the namespace vendor name from
  `Prometee\SyliusVIESClientPlugin` to `FluxSE\SyliusEUVatPlugin`
* **BC BREAK**: Rename classes from
  `PrometeeSyliusVIESClientPlugin` to `FluxSESyliusEUVatPlugin`
  `PrometeeSyliusVIESClientExtension` to `FluxSESyliusEUVatExtension`
* **BC BREAK**: Rename the service names from
  `flux_se.sylius_vies_client_plugin.*` to `flux_se.sylius_eu_vat.*`
* **BC BREAK**: Rename translations name from
  `flux_se_sylius_vies_client.*` to `flux_se.sylius_eu_vat`
* **BC BREAK**: Rename channel fixture name from
  `vies_plugin_european_channel` to `eu_vat_plugin_european_channel`

# UPGRADE FROM `v1.0.0` TO `v1.0.1`

* **BC BREAK**: `Prometee\SyliusVIESClientPlugin\Entity\Channel` have to be defined into your `src/Entity/Channel` directory 
* **BC BREAK**: `Prometee\SyliusVIESClientPlugin\Entity\ChannelInterface` have to be defined into your `src/Entity/Channel` directory 
* **BC BREAK**: `Prometee\SyliusVIESClientPlugin\Entity\Address` have to be defined into your `src/Entity/Addressing` directory
* **BC BREAK**: `Prometee\SyliusVIESClientPlugin\Entity\AddressInterface` have to be defined into your `src/Entity/Addressing` directory
