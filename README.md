# Module.Cyclops
Module for retrieving and sending data to cyclops

## Entities

* `CustomerEntity.php` Used for most of our use cases, this combines a CyclopsIdentityEntity with their BrandOptIn value.

* `CyclopsIdentityEntity.php` How we identify a Cyclops Customer - made up of email, id, forename and surname. 

* `CyclopsCustomerListEntity.php` Made up of an array of CustomerEntities, used in our "Push to Cyclops" use cases.

## Exceptions

Cyclops API has a few exceptions it throws for specific end points. The ones we handle are:

* `ConflictException.php` Thrown if the parameters we send are invalid.

* `CustomerNotFoundException.php` Thrown if the customer we are updating or deleting is not found in Cyclops API.

* `CyclopsException.php` Base exception that all others extend.

* `UserForbiddenException.php` Thrown if the user we access Cyclops API as does not have read or write permissions. 

## Settings
The url, username and password we use to access the Cyclops API are set in `CyclopsSettings.php` 

## Use Cases

* `DeleteCustomerUseCase.php`

* `GetBrandOptInStatusChangesUseCase.php`

* `GetBrandOptInUseCase.php`

* `PullChangesFromCyclopsUseCase.php`

* `PushDeletedToCyclopsUseCase.php`

* `PushStaleToCyclopsUseCase.php`
