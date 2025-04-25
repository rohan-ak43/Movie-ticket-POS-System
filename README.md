# Movie-ticket-POS-System
This project is a web-based Movie Ticket Booking System developed using PHP, MySQL, HTML, CSS, and JavaScript. It enables users to sign up, log in, and book seats for movies.
1. Multi-Language Full Stack Implementation:
• PHP: Manages backend logic such as user registration, authentication, seat
booking, and database operations.
• MySQL: Stores user accounts, movie details, and seat booking records.
• HTML/CSS: Provides structure and styling for the web pages, delivering a
clean and professional user interface.
• JavaScript: Handles dynamic interactivity, such as seat selection, live seat
availability updates, and AJAX communication.
2. User Authentication System:
• Signup Page:
o Allows new users to register using their email and password.
o Passwords are encrypted using password_hash() for security.
o Validates uniqueness of the email before account creation.
• Login Page:
o Verifies user credentials using password_verify().
o Maintains user sessions upon successful login using PHP sessions.
o Displays clear error messages for failed login attempts.
3. Secure Booking System:
• Ensures that each user can only book once for a particular movie in a
session.
• PHP sessions ($_SESSION) are used to track login status and user actions.
• Prevents users from selecting seats that have already been reserved.
4. Real-Time Seat Availability:
• Seat layout updates dynamically based on the selected movie.
• Seats already booked are shown as disabled and visually differentiated.
• Uses AJAX:
o GET requests to retrieve booked seat data from the database.
o POST requests to store newly selected seats into the database.
5. Interactive Seat Selection Grid:
• Seat map is generated using JavaScript, with labels like A1 to E8.
• Users can visually select multiple seats.
• Selected seats are highlighted for clear feedback.
• Seats that are booked are marked differently and are unselectable.

6. Booking Confirmation with Total Cost: 2
• Calculates the total cost based on:
o Number of seats selected
o Price per ticket for the selected movie
• After booking, a confirmation message is displayed showing:
o The selected seats
o Total price
o A random code to be shown at the venue for verification
7. Responsive and Modern UI:
• Designed with a dark theme using black background and gold highlights for
an elegant look.
• Uses clean fonts and spacing for better readability.
• Optimized for desktop viewing, with smooth alignment and user-friendly
layout.
8. Error Handling and User Feedback:
• Displays user-friendly messages for:
o Invalid inputs during signup or login
o Booking failures due to already reserved seats
o Successful booking confirmations
• Prevents empty form submissions and invalid seat selections.
9. Modular Code Structure:
• signup.php: Manages user registration.
• login.php: Handles user login and authentication.
• booking.php: Processes seat data requests and booking submissions.
• HTML, CSS, JavaScript, and PHP are separated for better maintainability.
