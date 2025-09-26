# 📝 Survey Tool

A powerful and flexible survey and feedback collection tool built with Laravel. Easily create surveys with multiple question types, collect responses from users, and analyze the results in a user-friendly dashboard.

---

## 🚀 Features

- 🔐 User Authentication (Login/Register)
- 📋 Survey creation with:
  - Multiple Choice Questions (MCQs)
  - Text-based Questions
  - Rating-based Questions
- 📤 Public Survey Sharing (via link)
- 📊 Response Collection & Result Analysis
- 🧩 Laravel MVC Architecture
- 🌐 Blade templating for frontend views
- ✅ Validation & Error Handling

---

## 🛠️ Tech Stack

- **Backend**: Laravel 10+
- **Frontend**: Blade Templates, Bootstrap
- **Database**: MySQL / MariaDB
- **Server**: PHP 8.x, Apache (via XAMPP)

---

## 📦 Installation

Follow these steps to set it up locally:

```bash
# 1. Clone the repository
git clone https://github.com/Oussama6776/Survey

# 2. Navigate to the project folder
cd Survey-tool

# 3. Install PHP dependencies
composer install

# 4. Copy the environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Set up the database (update DB info in .env file)

# 7. Run migrations
php artisan migrate

# 8. Serve the application
php artisan serve
 
app/                # Application logic (Controllers, Models)
resources/views/    # Blade templates for UI
routes/web.php      # Web routes
database/migrations # Table structures
public/             # Publicly accessible files
.env                # Environment variables (not pushed to GitHub)
 
👨‍💻 Author
Oussama Harouach
GitHub Profile
 