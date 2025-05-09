# LIFT (Logging Integrated Fitness Tracking)

Welcome to **LIFT**, a web-based system designed to streamline gym membership tracking and check-in/check-out logging. This project is the final group project for **ENGGSF** by Team **Inkedin**. The system is developed for **Steel Iron Master Gym** to provide an efficient solution for managing gym operations.


## 📋 Project Overview

LIFT is a web-based application that helps gym owners and staff:
- Track gym memberships
- Log member check-ins and check-outs
- Manage member details effectively

Our mission is to provide a reliable and user-friendly system that enhances the fitness journey for both gym owners and their members.


## ✨ Features

- **Membership Management**: Add, update(add months/renew), and delete member details.
- **Check-In/Check-Out System**: Log member and non-member visits with details for tracking.
- **Database Integration**: Efficient storage and retrieval of member information.
- **Responsive Design**: User-friendly and accessible on the web browser.
- **Error Handling**: Includes validation and feedback for user inputs.
- **Theme Options**: Dark and light mode to suit user preferences and accessibility.


## 👥 Authors

This project was proudly developed by **Team Inkedin**:
- Covacha, Erin Drew [@egwolk](https://github.com/egwolk)
- De Guzman, Jonah Andre [@jdgmn](https://github.com/jdgmn)
- Pablo, Lance Angelo [@aimgoinzane](https://github.com/aimgoinzane)
- Ragel, William Rap-El [@rap-el](https://github.com/rap-el)
- Robles, Stephen Ezekiel [@tpen14](https://github.com/tpen14)


## 🚀 Getting Started

Follow these instructions to run the system locally on your machine.

### Prerequisites
- Install **[XAMPP](https://www.apachefriends.org/index.html)**, which provides Apache, MySQL, and PHP.

### Setup Instructions
1. Clone this repository to your local machine:
   ```bash
   git clone https://github.com/jdgmn/InkedInLIFT.git
   ```
2. Move the project folder to the `htdocs` directory in your XAMPP installation: 
    ```plaintext
    C:/xampp/htdocs/InkedInLIFT
    ```
3. Start XAMPP and ensure that the **Apache** and **MySQL** modules are running.
4. Import the database:
   - Open [phpMyAdmin](http://localhost/phpmyadmin/).
   - Create a new database (e.g., `lift_db`).
   - Import the provided SQL file `lift_db (current).sql` into the database.
5. Configure the connection:
   - Open the project folder and locate the `dbcon.php` file.
   - Update the database credentials to match your local setup:
     ```php
     $host = 'localhost';
     $user = 'root';
     $password = ''; // Leave blank if no password
     $database = 'lift_db';
     ```
6. Access the system:
   - Open your web browser and navigate to:
     ```plaintext
     http://localhost/InkedInLIFT
     ```

## 🔧 Technologies Used

This project was built using the following technologies:
- **PHP**: Backend development
- **CSS**: Styling and layout
- **JavaScript**: Interactive features and functionality
- **MySQL**: Database management
- **XAMPP**: Local server environment

---

### Made with 💗 by Inkedin
