# Module.Cyclops
Module for retrieving and sending data to cyclops

## Commands

* `PushStateToCyclopsCommand.php` Abstract Custard Command for pushing the state of Cyclops Customers.

* `PushDeletedToCyclopsCommand.php` Deletes a list of Cyclops Customers.
 
* `PushStaleToCyclopsCommand.php` Updates a list of Cyclops Customers' brand opt ins that have failed before.

* `PullChangesFromCyclopsCommand.php` Retrieves a list of updates made to Cyclops Customers' brand opt ins, 
allowing us to update project specific members to the same status.

## Entities

* `CustomerEntity.php` Used for most of our use cases, this combines a CyclopsIdentityEntity with their BrandOptIn and 
Subscriptions values.

* `CyclopsIdentityEntity.php` How we identify a Cyclops Customer - made up of email, id, forename and surname. 

* `CyclopsCustomerListEntity.php` Made up of an array of CustomerEntities, used in our "Push to Cyclops" use cases.

* `SubscriptionEntity.php` Created when we were updating customers subscriptions, rather than brand opt ins. 
Made up of an id, name and subscription value.

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

* `GetSubscriptionListUseCase.php`

* `GetSubscriptionSettingsUseCase.php`

* `PullChangesFromCyclopsUseCase.php`

* `PushDeletedToCyclopsUseCase.php`

* `PushStaleToCyclopsUseCase.php`

* `SetBrandOptInUseCase.php`

* `SetSubscriptionSettingsUseCase.php`
