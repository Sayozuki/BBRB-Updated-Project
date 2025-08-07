<?php
require 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate inputs
    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Insert data into the database
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $message]);

            // Show a success message
            echo "<script>alert('Your message has been sent successfully!');</script>";
        } else {
            echo "<script>alert('Invalid email address. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Contact Us</title>

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
     <link href="css/contact.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <!-- Add this CSS to style the message textarea -->
    <style>
        .message-box {
            width: 100%;
            /* Make it take the full width of the container */
            height: 100px;
            /* Adjust the height to make it larger */
            padding: 10px;
            /* Add some padding for better readability */
            border: 1px solid #ccc;
            /* Add a border */
            border-radius: 10px;
            /* Add rounded corners */
            font-size: 16px;
            /* Increase the font size */
            resize: none;
            /* Disable resizing if you want a fixed size */
        }

        input[type="text"],
        input[type="email"],
        textarea {
            border-radius: 10px !important; /* Increase the value as desired */
        }
    </style>
</head>

<body class="sub_page">

    <div class="hero_area">
        <!-- header section starts -->
        <?php include 'header.php'; ?>
        <!-- end header section -->
    </div>

    <!-- contact section -->
    <section class="contact_section layout_padding">
        <div class="container">
            <div class="heading_container">
                <h2>Contact Us</h2>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <!-- Contact Form -->
                    <form action="contact.php" method="POST">
                        <div>
                            <input type="text" name="name" placeholder="Name" required />
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Email" required />
                        </div>
                        <div>
                            <input type="text" name="phone" placeholder="Phone Number" />
                        </div>
                        <div>
                            <textarea name="message" class="message-box" placeholder="Message" required></textarea>
                        </div>
                        <div class="d-flex">
                            <button type="submit">SEND</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <!-- Add the image to the right side -->
                    <img src="images/Map.png" alt="Map" style="width: 100%; height: auto; border-radius: 10px;">
                </div>
            </div>
        </div>
    </section>
    <!-- end contact section -->

    <!-- info section -->
    <?php include 'info_section.php'; ?>
    <!-- end info section -->

    <!-- footer section -->
    <?php include 'footer.php'; ?>
    <!-- footer section -->

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

</body>

</html>