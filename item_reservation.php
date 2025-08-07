<!-- filepath: d:\XAMPP\htdocs\Barangay_system\item_reservation.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reservation'])) {
    $item = $_POST['item'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $quantity = $_POST['quantity'];
    $price_estimate = $_POST['price_estimate']; // Use the value from the form
    $reason = $_POST['reason'];
    $additional_note = $_POST['additional_note'];

    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, type, details, start_date, end_date, quantity, price_estimate, reason, additional_note) VALUES (?, 'item', ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $item, $start_date, $end_date, $quantity, $price_estimate, $reason, $additional_note]);

    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Reservation</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/reservation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Reserve Items</h2>
        <form id="reservationForm" method="POST">
            <div class="form-group">
                <label for="item">Items</label>
                <select name="item" id="item" class="form-control" required>
                    <option value="" disabled selected>Select an Item</option>
                    <option value="Sound System">Sound System</option>
                    <option value="Projector with Screen">Projector with Screen</option>
                    <option value="Life Time Table">Life Time Table</option>
                    <option value="Life Time Chair">Life Time Chair</option>
                    <option value="Long Table">Long Table</option>
                    <option value="Monoblock Chair">Monoblock Chair</option>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason</label>
                <select name="reason" id="reason" class="form-control" required>
                     <option value="Birthday">Birthday</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Events">Events</option>
                    <option value="Sports">Sports</option>
                    <option value="Gathering">Gathering</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Conference">Conference</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Festival">Festival</option>
            </select>
            </div>
            <div class="form-group">
                <label for="additional_note">Additional Note</label>
                <textarea name="additional_note" id="additional_note" class="form-control"></textarea>
            </div>
            <!-- Payment Fee Display -->
            <div class="form-group">
                <p id="payment-fee-display">Payment Fee: </p>
            </div>
            <input type="hidden" name="price_estimate" id="price_estimate">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmationModal">Submit Reservation</button>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Reservation Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Item:</strong> <span id="confirmItem"></span></p>
                    <p><strong>Start Date:</strong> <span id="confirmStartDate"></span></p>
                    <p><strong>End Date:</strong> <span id="confirmEndDate"></span></p>
                    <p><strong>Quantity:</strong> <span id="confirmQuantity"></span></p>
                    <p><strong>Reason:</strong> <span id="confirmReason"></span></p>
                    <p><strong>Additional Note:</strong> <span id="confirmAdditionalNote"></span></p>
                    <p><strong>Payment Fee:</strong> <span id="confirmPaymentFee"></span></p>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirmCheckbox" required>
                        <label class="form-check-label" for="confirmCheckbox">I confirm that the details I entered are correct.</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="reservationForm" name="confirm_reservation" class="btn btn-primary">Confirm Reservation</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const reservationForm = document.getElementById('reservationForm');
        const confirmItem = document.getElementById('confirmItem');
        const confirmStartDate = document.getElementById('confirmStartDate');
        const confirmEndDate = document.getElementById('confirmEndDate');
        const confirmQuantity = document.getElementById('confirmQuantity');
        const confirmReason = document.getElementById('confirmReason');
        const confirmAdditionalNote = document.getElementById('confirmAdditionalNote');
        const paymentFeeDisplay = document.getElementById('payment-fee-display');
        const confirmPaymentFee = document.getElementById('confirmPaymentFee');

        // ✅ Set minimum date to today for start and end date inputs
        const today = new Date();
        today.setMinutes(today.getMinutes() - today.getTimezoneOffset()); // Adjust for timezone
        const minDateTime = today.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:mm"

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput && endDateInput) {
            startDateInput.min = minDateTime;
            endDateInput.min = minDateTime;
        }

        function calculatePaymentFee() {
            const item = document.getElementById('item').value;
            const qty = parseInt(document.getElementById('quantity').value) || 0;
            let price = 0;
            switch (item) {
                case 'Sound System': price = 1000; break;
                case 'Projector with Screen': price = 1500; break;
                case 'Life Time Table': price = 150; break;
                case 'Life Time Chair': price = 50; break;
                case 'Long Table': price = 200; break;
                case 'Monoblock Chair': price = 10; break;
                default: price = 0;
            }
            const total = price * qty;
            document.getElementById('price_estimate').value = total;
            if (item && qty > 0) {
                paymentFeeDisplay.textContent = 'Payment Fee: ₱' + total.toLocaleString();
                if (confirmPaymentFee) {
                    confirmPaymentFee.textContent = '₱' + total.toLocaleString();
                }
            } else {
                paymentFeeDisplay.textContent = 'Payment Fee: ';
                if (confirmPaymentFee) {
                    confirmPaymentFee.textContent = '';
                }
            }
        }

        document.getElementById('item').addEventListener('change', calculatePaymentFee);
        document.getElementById('quantity').addEventListener('input', calculatePaymentFee);
        calculatePaymentFee();

        // Show confirmation modal with updated details
        document.querySelector('[data-target="#confirmationModal"]').addEventListener('click', function () {
            confirmItem.textContent = document.getElementById('item').value;
            confirmStartDate.textContent = document.getElementById('start_date').value;
            confirmEndDate.textContent = document.getElementById('end_date').value;
            confirmQuantity.textContent = document.getElementById('quantity').value;
            confirmReason.textContent = document.getElementById('reason').value;
            confirmAdditionalNote.textContent = document.getElementById('additional_note').value;
            calculatePaymentFee();
        });

        reservationForm.addEventListener('submit', function (event) {
            if (!document.getElementById('confirmCheckbox').checked) {
                event.preventDefault();
                alert('Please confirm the details before submitting.');
            }
        });
    });
</script>

</body>

</html>