# Change Summary

This file summarizes the code batches changed for the inventory-backed ordering update.

## Deleted

### Old product seed batch
- Removed the previous `ProductSeeder` contents that seeded the old 5 demo products:
  - `Classic Chicken Burger`
  - `Creamy Carbonara Pasta`
  - `Pepperoni Pizza Slice`
  - `Loaded Fries`
  - `Iced Coffee`
- Replaced that old seed dataset with a new inventory-driven product dataset.

### Old order controller implementation
- Deleted the previous `OrderController` implementation and replaced it with a new version that:
  - calculates product availability from `ProductInventory`
  - blocks ordering expired or zero-stock items
  - deducts stock when orders are placed
  - keeps the existing customer, order, and order-details logging flow

## Added

### New inventory seeder
- Added [database/seeders/ProductInventorySeeder.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/database/seeders/ProductInventorySeeder.php:1)
- Seeds inventory batches that simulate:
  - one product with `100` available stock
  - one product with `2` available stock
  - one product with `0` stock
  - one product with expired stock

### Product and inventory model relationship support
- Added `inventories()` relation to [app/Models/Product.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Models/Product.php:22)
- Added `product()` relation to [app/Models/ProductInventory.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Models/ProductInventory.php:22)

### New inventory-aware order behavior
- Added inventory-aware product loading in [app/Http/Controllers/OrderController.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Http/Controllers/OrderController.php:19)
- Added computed per-product fields during checkout rendering:
  - `available_quantity`
  - `is_available`
  - `display_price`
- Added stock consumption logic through `consumeInventory()`
- Added runtime protection for stock changes during checkout using a transaction and exception handling

## Replaced

### Product seed data
- Replaced the old 5-product seed batch in [database/seeders/ProductSeeder.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/database/seeders/ProductSeeder.php:1)
- New seeded products are:
  - `Garlic Butter Steak Bowl`
  - `Truffle Mushroom Pasta`
  - `Smoked Chicken Wrap`
  - `Citrus Iced Tea`
- The new seed also clears matching product inventory rows for those products before inserting the new product records.

### Database seeding flow
- Updated [database/seeders/DatabaseSeeder.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/database/seeders/DatabaseSeeder.php:12)
- Replaced the old seed flow:
  - `ProductSeeder` only
- With the new seed flow:
  - `ProductSeeder`
  - `ProductInventorySeeder`

### Order page UI
- Replaced the menu card behavior in [resources/views/order.blade.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/resources/views/order.blade.php:75)
- Products now:
  - stay visible even when unavailable
  - show remaining stock
  - display `NOT AVAILABLE` when they have no valid remaining stock
  - disable the quantity input when unavailable
  - clamp entered quantity to the available stock on the client side

### Order validation and checkout rules
- Replaced the previous order validation in [app/Http/Controllers/OrderController.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Http/Controllers/OrderController.php:29)
- Old behavior:
  - validated customer fields
  - allowed ordering any active product
  - did not inspect inventory
  - did not deduct stock
- New behavior:
  - validates integer quantities
  - rejects unavailable products
  - rejects quantities above remaining valid stock
  - excludes expired inventory from sellable stock
  - deducts stock batch-by-batch after order submission

### Discount pricing logic
- Replaced the previous discount resolution logic in [app/Http/Controllers/OrderController.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Http/Controllers/OrderController.php:242)
- Old behavior:
  - used sale price whenever `ProductOnDiscount = 1` and `ProductPriceSale` was present
- New behavior:
  - only uses sale price when today falls within the configured discount start and end dates

### Admin inventory repository behavior
- Replaced incorrect inventory repository field handling in [app/Repositories/EloquentProductInventoryRepository.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Repositories/EloquentProductInventoryRepository.php:27)
- Old behavior:
  - attempted to write `ProductInventoryID`
  - attempted to write `ProductInventoryUpdateDate`
  - did not import `Str`
- New behavior:
  - writes the correct `ProductBatchID`
  - imports `Str` correctly

### Product repository timestamp field
- Replaced the incorrect product timestamp field usage in [app/Repositories/EloquentProductRepository.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Repositories/EloquentProductRepository.php:27)
- Old behavior:
  - wrote `ProductUpdateDate`
- New behavior:
  - writes `ProductUpdatedDate`

### Admin inventory validation
- Replaced loose validation rules in [app/Http/Controllers/Admin/ProductInventoryController.php](/c:/Users/Deanna Jeanne/Desktop/is226-project/restawran/app/Http/Controllers/Admin/ProductInventoryController.php:44)
- Old behavior:
  - accepted any `ProductID` string
  - accepted generic numeric quantity
  - did not validate batch date order
- New behavior:
  - requires `ProductID` to exist in `Product`
  - requires integer quantity with minimum `0`
  - requires expiry date to be on or after delivery date

## Seeded Inventory Outcome

After seeding, the intended product availability behavior is:

- `Garlic Butter Steak Bowl`: available with `100`
- `Truffle Mushroom Pasta`: available with `2`
- `Smoked Chicken Wrap`: visible but not available because stock is `0`
- `Citrus Iced Tea`: visible but not available because inventory is expired

## Verification Performed

- Ran PHP syntax checks on all touched PHP files
- Ran `php artisan db:seed --force` successfully
