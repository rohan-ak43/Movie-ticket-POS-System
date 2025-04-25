<?php
session_start();

// Connect to database
$host = "localhost";
$username = "root";
$password = "";
$database = "movie_booking";

$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// Handle GET request: fetch booked seats for a showtime
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["showtime"])) {
    $showtime_id = intval($_GET["showtime"]);
    $result = $mysqli->query("SELECT seat FROM bookings WHERE showtime_id = $showtime_id");
    $bookedSeats = [];
    while ($row = $result->fetch_assoc()) {
        $bookedSeats[] = $row["seat"];
    }
    echo json_encode($bookedSeats);
    exit;
}

// Handle POST request: book tickets
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["showtime"]) && isset($_POST["seats"])) {
    $showtime_id = intval($_POST["showtime"]);
    $seats = explode(",", $_POST["seats"]);

    // Get price from movie linked to showtime
    $stmt = $mysqli->prepare("SELECT m.price FROM showtimes s JOIN movies m ON s.movie_id = m.id WHERE s.id = ?");
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        echo "Invalid showtime.";
        exit;
    }
    $row = $result->fetch_assoc();
    $price = $row["price"];

    // Prevent double booking
    if (isset($_SESSION["booked"][$showtime_id])) {
        echo "You've already booked tickets for this showtime.";
        exit;
    }

    // Insert bookings
    foreach ($seats as $seat) {
        $seat = $mysqli->real_escape_string($seat);
        $stmt = $mysqli->prepare("INSERT INTO bookings (movie, seat, price, showtime_id) 
            VALUES ((SELECT m.title FROM showtimes s JOIN movies m ON s.movie_id = m.id WHERE s.id = ?), ?, ?, ?)");
        $stmt->bind_param("ssii", $showtime_id, $seat, $price, $showtime_id);
        $stmt->execute();
    }

    $_SESSION["booked"][$showtime_id] = true;
    echo "Booking successful! Total ₹" . (count($seats) * $price);
    echo "\nShow the random code and pay at the venue";
    exit;
}

// Default: show booking form
$moviesWithShowtimes = [];
$result = $mysqli->query("SELECT s.id AS showtime_id, m.title, s.show_time, m.price 
                          FROM showtimes s 
                          JOIN movies m ON s.movie_id = m.id 
                          ORDER BY m.title, s.show_time");

while ($row = $result->fetch_assoc()) {
    $moviesWithShowtimes[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movie Ticket Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #000;
            color: #ffd700;
            text-align: center;
            padding: 20px;
        }
        select, button {
            font-size: 16px;
            margin: 10px;
            padding: 5px 10px;
        }
        .seats {
            display: grid;
            grid-template-columns: repeat(10, 40px);
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .seat {
            width: 40px;
            height: 40px;
            background: #333;
            color: #fff;
            line-height: 40px;
            cursor: pointer;
            border-radius: 5px;
        }
        .seat.selected {
            background: #ffd700;
            color: #000;
        }
        .seat.booked {
            background: red;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Movie Ticket Booking</h1>

    <label for="showtime">Select Movie Showtime:</label>
    <select id="showtime">
        <?php foreach ($moviesWithShowtimes as $item): ?>
            <option value="<?= $item['showtime_id'] ?>" data-price="<?= $item['price'] ?>">
                <?= $item['title'] ?> - <?= date("M d, h:i A", strtotime($item['show_time'])) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="seats" id="seatsContainer"></div>

    <button id="bookBtn">Book Tickets</button>

    <div id="output"></div>

    <script>
        function generateSeats(booked) {
            const container = document.getElementById("seatsContainer");
            container.innerHTML = "";
            for (let i = 1; i <= 50; i++) {
                const seatNum = i.toString().padStart(2, "0");
                const seat = document.createElement("div");
                seat.className = "seat";
                seat.textContent = seatNum;
                seat.dataset.seat = seatNum;
                if (booked.includes(seatNum)) {
                    seat.classList.add("booked");
                } else {
                    seat.addEventListener("click", () => {
                        seat.classList.toggle("selected");
                    });
                }
                container.appendChild(seat);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const showtimeSelect = document.getElementById("showtime");

            function loadSeats() {
                const showtimeId = showtimeSelect.value;
                fetch(booking.php?showtime=${encodeURIComponent(showtimeId)})
                    .then(res => res.json())
                    .then(booked => generateSeats(booked))
                    .catch(err => console.error(err));
            }

            loadSeats();
            showtimeSelect.addEventListener("change", () => {
                loadSeats();
                document.getElementById("output").innerHTML = "";
            });

            document.getElementById("bookBtn").addEventListener("click", () => {
                const showtimeId = showtimeSelect.value;
                const selectedSeats = Array.from(document.querySelectorAll(".seat.selected")).map(seat => seat.dataset.seat);
                const quantity = selectedSeats.length;

                if (quantity === 0) {
                    document.getElementById("output").innerHTML = "Please select at least one seat.";
                    return;
                }

                fetch("booking.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: showtime=${encodeURIComponent(showtimeId)}&seats=${encodeURIComponent(selectedSeats.join(","))}
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById("output").innerHTML = data;
                    loadSeats();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>