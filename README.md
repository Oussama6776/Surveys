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
git clone https://github.com/Narasimhagupta2004/Survey-tool.git

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

🧪 Testing
You can use Laravel's built-in testing tools or test it manually by:

Registering a new user

Creating surveys

Responding to surveys via public links

Viewing collected responses

🙌 Contribution
Feel free to fork this repo and contribute. Pull requests are welcome!

📃 License
This project is open-source and available under the MIT License.

👨‍💻 Author
Narasimha Gupta
GitHub Profile




