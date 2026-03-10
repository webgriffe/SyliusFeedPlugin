# Upgrade plugin guide

## Upgrade from version 06.x to 1.x

In this version, we have updated the plugin to be compatible with version 2 of Sylius.

- The route `@SetonoSyliusFeedPlugin/Resources/config/routing.yaml` and
  `SetonoSyliusFeedPlugin/Resources/config/routing_non_localized.yaml` does not exist anymore. You should explicitly
  import the routes for the feed and admin in your `config/routes.yaml` file as follows:
  ```yaml
  setono_sylius_feed_admin:
      resource: "@SetonoSyliusFeedPlugin/config/routes/admin.php"
      prefix: '/%sylius_admin.path_name%'
  
  setono_sylius_feed_shop:
      resource: "@SetonoSyliusFeedPlugin/config/routes/shop.php"
  ```
- The file `@SetonoSyliusFeedPlugin/Resources/config/app/config.yaml` has been replaced by `@SetonoSyliusFeedPlugin/Resources/config/app/config.yaml`. You should explicitly import the configuration for the feed in your `config/packages/setono_sylius_feed.yaml` file as follows:
  ```yaml
  imports:
      - { resource: "@SetonoSyliusFeedPlugin/config/config.php" }
  ```
- The file `@SetonoSyliusFeedPlugin/Feed/Google/Shopping/feed.txt.twig` has been replaced by `@SetonoSyliusFeedPlugin/feed/Google/Shopping/feed.txt.twig`.
