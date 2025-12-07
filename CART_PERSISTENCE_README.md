# Persistent Cart Setup

## What Changed
The shopping cart now persists across sessions! Users can log out and their cart items will be saved and restored when they log back in.

## Database Migration Required
Run this SQL command in your MySQL database to add the cart storage column:

```sql
ALTER TABLE users 
ADD COLUMN cart JSON DEFAULT NULL COMMENT 'Stores user shopping cart as JSON array';

UPDATE users SET cart = JSON_ARRAY() WHERE cart IS NULL;
```

Or run the migration file:
```bash
mysql -u your_username -p your_database < database_scripts/add_cart_column.sql
```

## How It Works

1. **On Login**: Cart from database is loaded and merged with any items in the session
2. **Adding Items**: Cart is saved to database immediately (for logged-in users)
3. **Updating Cart**: All changes (increase/decrease/remove) are saved to database
4. **Checkout**: Cart is cleared from both session and database
5. **Guest Users**: Cart stays in session only (not saved until they log in)

## Files Modified

- `cart_db.php` (NEW) - Helper functions for cart database operations
- `addtocart.inc.php` - Saves cart after adding items
- `updatecart.inc.php` - Saves cart after quantity/removal changes  
- `processorder.inc.php` - Clears cart from database after order
- `validateuser.inc.php` - Loads cart from database on login
- `add_cart_column.sql` (NEW) - Database migration script

## Benefits

- ✅ Cart persists across browser sessions
- ✅ Cart syncs across devices for same user
- ✅ Abandoned cart recovery possible
- ✅ Better user experience - no lost items on logout
