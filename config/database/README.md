# StreamFlix Database Setup

## Files Description

1. **create_tables.sql** - Creates all required tables for StreamFlix
2. **seed_data.sql** - Inserts sample data for development and testing

## How to Import

### Method 1: Using phpMyAdmin
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "Import" tab
3. Choose `create_tables.sql` first
4. Click "Go" to execute
5. Then import `seed_data.sql`

### Method 2: Using MySQL Command Line
```bash
mysql -u root -p < create_tables.sql
mysql -u root -p < seed_data.sql
```

### Method 3: Using Laragon Terminal
1. Open Laragon
2. Click "Terminal"
3. Navigate to database folder:
   ```bash
   cd c:\laragon\www\netflix_lite\database
   ```
4. Execute SQL files:
   ```bash
   mysql -u root -p < create_tables.sql
   mysql -u root -p < seed_data.sql
   ```

## Default Login Credentials

### Admin Panel
- **Username:** admin
- **Email:** admin@streamflix.com  
- **Password:** password

### Sample Users
- **Email:** john.doe@email.com
- **Password:** password

## Database Structure

- **Database Name:** streamflix
- **Users:** 5 sample users
- **Movies:** 20 popular movies with YouTube trailers
- **Genres:** 10 movie genres
- **Cast & Crew:** 10 famous actors/directors
- **Reviews:** 10 sample reviews
- **Watchlist:** Sample watchlist data

## Notes

- All passwords are hashed using PHP's `password_hash()` function
- YouTube video IDs are real trailer IDs
- Database includes proper foreign key relationships
- Sample data is ready for development and testing
