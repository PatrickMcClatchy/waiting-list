# Waiting List Application

A web-based waiting list application with an admin page for managing entries. This application helps keep track of people waiting for a service or event, allowing admins to reorder or remove entries from the list.

## Features

- **User List Management**: Add users to the waiting list.
- **Admin Control**: Admins can reorder and remove entries as needed.
- **Database**: Uses an SQLite database (`waiting_list.db`) to store user data.
- **PHP-Based**: The application is written in PHP and is compatible with most servers that support PHP.

## File Structure

- `index.php`: Main page where users can add themselves to the waiting list.
- `admin.php`: Admin interface for viewing, reordering, and removing users from the list.
- `add_user.php`: Script to handle adding users to the list.
- `get_list.php`: Retrieves the current list for display.
- `waiting_list.db`: SQLite database file that stores all list data.

## Installation

1. **Clone this repository**: Download the project files by cloning the repository to your local machine:
   ```bash
   git clone https://github.com/PatrickMcClatchy/waiting-list.git

2. **Upload files to your server**: Transfer the project files to a PHP-enabled server.

3. **Set permissions**: Ensure PHP has read and write permissions for `waiting_list.db` to allow the application to store data.

## Usage

1. **Adding Users**: Go to `index.php`, where users can add themselves to the waiting list by filling out the provided form.

2. **Admin Management**: Access `admin.php` to view and manage the waiting list. The admin page allows you to reorder entries or remove users from the list as necessary.

## Requirements

- PHP version 7.0 or higher
- SQLite support enabled in PHP

## License

This project is open-source and available under the MIT License.
