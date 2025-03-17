# Waiting List Application

This application manages a waiting list, allowing users to sign up and admins to add, remove, and reorder users. It includes features for scheduled opening times and manual closing of the list.

## Project Structure

```
project_root/
├── public/                   # Public files for users and admins
│   ├── index.html            # Main page for users to join the waiting list
│   ├── admin_dashboard.html  # Admin dashboard to manage the list
│   ├── api_proxy.php         # Proxy to access backend API
│   ├── css/                  # CSS files for styling
│   ├── error_log.txt         # Log file for errors
│   ├── export_waiting_list.html # Page to export the waiting list
│   ├── install_composer.php  # Script to install Composer dependencies
│   ├── logged_out.html       # Page displayed after logging out
│   ├── login.html            # Admin login page
│   └── main.css              # Main CSS file for styling
├── api/                      # Backend functionality (API endpoints)
│   ├── add_user.php          # Adds a user to the waiting list
│   ├── add_user_admin.php    # Adds a user to the waiting list (admin)
│   ├── check_session.php     # Checks if the admin session is active
│   ├── clear_waiting_list.php# Clears the waiting list
│   ├── config.php            # Configuration file
│   ├── create_db.php         # Script to create the database
│   ├── generate_pdf.php      # Generates a PDF confirmation for the user
│   ├── get_closed_message.php# Retrieves the closed message
│   ├── get_scheduled_open_times.php # Retrieves scheduled open times
│   ├── get_waiting_list.php  # Fetches the waiting list
│   ├── get_waiting_list_state.php # Fetches the state of the waiting list
│   ├── login.php             # Processes admin login
│   ├── logout.php            # Processes admin logout
│   ├── move_user.php         # Moves a user up or down in position
│   ├── remove_user.php       # Removes a user by ID
│   ├── scheduled_open.php    # Checks and updates the scheduled open times
│   ├── toggle_waiting_list.php # Toggles the waiting list state
│   ├── update_closed_message.php # Updates the closed message
│   ├── update_scheduled_open_times.php # Updates scheduled open times
│   └── update_waiting_list_state.php # Updates the waiting list state
├── waiting_list.db           # SQLite database file
├── composer.json             # Composer configuration file
├── composer.lock             # Composer lock file
└── README.md                 # Project documentation
```

## Key Features

- **User Sign-Up**: Public users can access `index.html` to join the waiting list.
- **Admin Dashboard**: `admin_dashboard.html` provides an interface for admins to manage the list by adding, removing, or reordering users.
- **API Proxy**: `api_proxy.php` routes frontend requests to backend functionality located in the `api` folder.
- **PDF Generation**: Generates a PDF confirmation for users who sign up.
- **Session Management**: Handles admin login sessions and checks session validity.
- **Database Management**: Uses SQLite for storing user data and application settings.
- **Scheduled Open Times**: Allows admins to set scheduled open times for the waiting list.
- **Manual Close Override**: Prevents the list from reopening on the same day if it was manually closed.
- **Closed Message**: Displays a custom message when the waiting list is closed.

## Setup Instructions

1. Clone the repository to your local machine.
2. Ensure you have PHP and SQLite installed.
3. Set up the SQLite database (`waiting_list.db`) in the project root by running the `create_db.php` script:
   ```bash
   php api/create_db.php
   ```
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
- **Check Session**: `api_proxy.php?endpoint=check_session.php`
- **Clear Waiting List**: `api_proxy.php?endpoint=clear_waiting_list.php`
- **Generate PDF**: `api_proxy.php?endpoint=generate_pdf.php`
- **Get Closed Message**: `api_proxy.php?endpoint=get_closed_message.php`
- **Get Scheduled Open Times**: `api_proxy.php?endpoint=get_scheduled_open_times.php`
- **Get Waiting List State**: `api_proxy.php?endpoint=get_waiting_list_state.php`
- **Login**: `api_proxy.php?endpoint=login.php`
- **Logout**: `api_proxy.php?endpoint=logout.php`
- **Scheduled Open**: `api_proxy.php?endpoint=scheduled_open.php`
- **Toggle Waiting List**: `api_proxy.php?endpoint=toggle_waiting_list.php`
- **Update Closed Message**: `api_proxy.php?endpoint=update_closed_message.php`
- **Update Scheduled Open Times**: `api_proxy.php?endpoint=update_scheduled_open_times.php`
- **Update Waiting List State**: `api_proxy.php?endpoint=update_waiting_list_state.php`

## Contributing

When contributing:
1. Organize frontend (public-facing) and backend (API) code as shown.
2. Use `api_proxy.php` for all frontend requests to the backend.
3. Test that frontend requests are successfully connecting to backend functions.
4. Follow best practices for security, error handling, and code organization.

## Security Considerations

- **CSRF Protection**: Implement CSRF protection for all forms and API endpoints.
- **Sensitive Information**: Use environment variables for sensitive information like the reCAPTCHA secret key and admin password hash.
- **Session Management**: Improve session management by regenerating session IDs on login and setting secure cookie flags.
- **Error Handling**: Implement consistent error handling and logging throughout the application.
- **Input Validation and Sanitization**: Enhance input validation and sanitization to handle more edge cases and prevent potential security issues.

By following these guidelines, the project can be made more secure, maintainable, and suitable for production use.