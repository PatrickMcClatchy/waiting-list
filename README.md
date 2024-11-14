# Waiting List Application

This application manages a waiting list, allowing users to sign up and admins to add, remove, and reorder users.

## Project Structure

```
project_root/
├── public/                   # Public files for users and admins
│   ├── index.html            # Main page for users to join the waiting list
│   ├── admin_dashboard.html  # Admin dashboard to manage the list
│   ├── api_proxy.php         # Proxy to access backend API
│   └── css/                  # Optional CSS files
├── api/                      # Backend functionality (API endpoints)
│   ├── add_user.php          # Adds a user to the waiting list
│   ├── remove_user.php       # Removes a user by ID
│   ├── move_user.php         # Moves a user up or down in position
│   ├── get_waiting_list.php  # Fetches the waiting list
│   ├── login.php             # Processes admin login
│   ├── logout.php            # Processes admin logout
└── waiting_list.db           # SQLite database file
```

## Key Features

- **User Sign-Up**: Public users can access `index.html` to join the waiting list.
- **Admin Dashboard**: `admin_dashboard.html` provides an interface for admins to manage the list by adding, removing, or reordering users.
- **API Proxy**: `api_proxy.php` routes frontend requests to backend functionality located in the `api` folder.

## Setup Instructions

1. Clone the repository to your local machine.
2. Ensure you have PHP and SQLite installed.
3. Set up the SQLite database (`waiting_list.db`) in the project root.

4. Start a local server with PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Open `http://localhost:8000` in your browser to access the application.

## Deployment

1. Upload the contents of the repository to your server.
2. Set the document root of your web server to the `public` folder.
3. Ensure the server has PHP and SQLite installed.
4. Make sure the `waiting_list.db` file is readable and writable by the server, and that the API folder and its files are secured from direct access.

## API Endpoints

Each backend function has its own file in the `api` folder, accessed through `api_proxy.php`:
- **Add User**: `api_proxy.php?endpoint=add_user.php`
- **Remove User**: `api_proxy.php?endpoint=remove_user.php`
- **Move User**: `api_proxy.php?endpoint=move_user.php`
- **Get Waiting List**: `api_proxy.php?endpoint=get_waiting_list.php`

## Contributing

When contributing:
1. Organize frontend (public-facing) and backend (API) code as shown.
2. Use `api_proxy.php` for all frontend requests to the backend.
3. Test that frontend requests are successfully connecting to backend functions.
