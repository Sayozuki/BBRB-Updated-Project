<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/reservation.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="content-wrapper">
        <div class="container mt-5">
            <h2 class="text-center">Reservation</h2>
            <p class="text-center text-muted mt-2">
                Choose a category below to reserve facilities or items for your event. Please ensure you agree to the terms and conditions before proceeding.
            </p>
            <div class="row justify-content-center mt-4">
                <!-- Card for Reserve Facilities -->
                <div class="col-md-4">
                    <div class="card text-center" id="openHelloWorldModal" data-target="facility_reservation.php">
                        <div class="card-icon mt-3">
                            <i class="bi bi-building" style="font-size: 6rem; color: #001f3f;"></i> <!-- Bootstrap Icon -->
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Reserve Facilities</h5>
                        </div>
                    </div>
                </div>
                <!-- Card for Reserve Items -->
                <div class="col-md-4">
                    <div class="card text-center" id="openHelloWorldModal" data-target="item_reservation.php">
                        <div class="card-icon mt-3">
                            <i class="bi bi-box" style="font-size: 6rem; color: #001f3f;"></i> <!-- Bootstrap Icon -->
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Reserve Items</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Hello World Modal -->
    <div id="helloWorldModal" class="custom-popup">
        <div class="custom-popup-content">
            <div class="custom-popup-header">
                <h5>Terms and Conditions</h5>
            </div>
            <div class="custom-popup-body">
                <?php
                $terms = file_get_contents('terms_and_conditions.txt');
                echo nl2br(htmlspecialchars($terms));
                ?>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="termsCheckbox">
                    <label class="form-check-label" for="termsCheckbox">I agree to the terms and conditions</label>
                </div>
                <p id="warningMessage" class="dark-red mt-2" style="display: none;">You must agree to the terms and
                    conditions before proceeding.</p>
            </div>
            <div class="custom-popup-footer">
                <button type="button" class="btn btn-primary" id="proceedButton">Proceed</button>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Custom Modal Styles -->
    <style>
        .custom-popup {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .custom-popup-content {
            background: #fff;
            border-radius: 8px;
            width: 400px;
            max-width: 90vw;
            min-height: 200px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .custom-popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
        }

        .custom-popup-body {
            padding: 16px 20px;
        }

        .custom-popup-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 8px 20px;
            border-top: 1px solid #eee;
        }

        .custom-popup-close {
            cursor: pointer;
            font-size: 1.5rem;
            color: #888;
        }
    </style>

    <!-- Custom Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openModalButtons = document.querySelectorAll('#openHelloWorldModal');
            const modal = document.getElementById('helloWorldModal');
            const closeModalButtons = document.querySelectorAll('#closeHelloWorldModal');
            const proceedButton = document.getElementById('proceedButton');
            const termsCheckbox = document.getElementById('termsCheckbox');
            const warningMessage = document.getElementById('warningMessage');

            let redirectUrl = '';

            openModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    redirectUrl = button.getAttribute('data-target'); // Get the target URL
                    modal.style.display = 'flex';
                });
            });

            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            });

            // Close modal when clicking outside the content
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Handle Proceed button click
            proceedButton.addEventListener('click', () => {
                if (termsCheckbox.checked) {
                    window.location.href = redirectUrl; // Redirect to the target URL
                } else {
                    warningMessage.style.display = 'block'; // Show warning message
                }
            });

            // Hide warning message when checkbox is checked
            termsCheckbox.addEventListener('change', () => {
                if (termsCheckbox.checked) {
                    warningMessage.style.display = 'none';
                }
            });
        });
    </script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>