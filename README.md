ORM Feature Flags Storage
================

Using [Doctrine](https://www.doctrine-project.org/) to store [Twentytwo Labs Feature Flags](https://github.com/TwentytwoLabs/feature-flag-bundle).

Configuration
-----------

```
# config/packages/twentytwo_labs_feature_flag.yaml
twentytwo_labs_feature_flag:
   managers:
      admin:
         factory: twenty-two-labs.feature-flags.factory.orm
         options:
            class: ENTITY
```

where:
- `ENTITY` is an Entity witch implement [`FeatureInterface`](https://github.com/TwentytwoLabs/feature-flag-bundle/blob/master/src/Model/FeatureInterface.php)


