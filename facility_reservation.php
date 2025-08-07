<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reservation'])) {
    $facility = $_POST['facility'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price_estimate = isset($_POST['price_estimate']) ? $_POST['price_estimate'] : 0;
    $reason = $_POST['reason'];
    $additional_note = $_POST['additional_note'];

    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, type, details, start_date, end_date, price_estimate, reason, additional_note) VALUES (?, 'facility', ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $facility, $start_date, $end_date, $price_estimate, $reason, $additional_note]);

    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Reservation</title>
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
        <h2 class="text-center">Reserve a Facility</h2>
        <form id="reservationForm" method="POST">
            <div class="form-group">
                <label for="facility">Facility</label>
                <select name="facility" id="facility" class="form-control" required>
                    <option value="" disabled selected>Select an Facility</option>
                    <option value="Basketball Court">Basketball Court</option>
                    <option value="Volleyball Court">Volleyball Court</option>
                    <option value="Badminton Court">Badminton Court</option>
                    <option value="Whole Court">Whole Court</option>
                    <option value="Bulwagan with Aircon">Bulwagan with Aircon</option>
                    <option value="Bulwagan without Aircon">Bulwagan without Aircon</option>
                    <option value="Parking">Parking</option>
                    <option value="Session Hall">Session Hall</option>
                    <option value="Playground">Playground</option>
                    <option value="Conference Room">Conference Room</option>
                    <option value="Small Meeting Room">Small Meeting Room</option>
                    <option value="Community Center with Aircon">Community Center with Aircon</option>
                    <option value="Community Center without Aircon">Community Center without Aircon</option>
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
            <div class="form-group dropdown-container">
                <label for="reason">Reason</label>
                <select name="reason" id="reason" class="form-control" required>
                    <option value="Birthday">Birthday</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Events">Events</option>
                    <option value="Sports">Sports</option>
                    <option value="Gathering">Gathering</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Conference">Conference</option>
                    <option value="Celebration">Celebration</option>
                    <option value="Anniversary">Anniversary</option>
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
            <input type="hidden" name="price_estimate" id="price_estimate" value="">
            <button type="button" class="btn btn-primary" id="showConfirmationBtn">Submit Reservation</button>
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
                    <p><strong>Facility:</strong> <span id="confirmFacility"></span></p>
                    <p><strong>Start Date:</strong> <span id="confirmStartDate"></span></p>
                    <p><strong>End Date:</strong> <span id="confirmEndDate"></span></p>
                    <p><strong>Reason:</strong> <span id="confirmReason"></span></p>
                    <p><strong>Additional Note:</strong> <span id="confirmAdditionalNote"></span></p>
                    <p><strong>Payment Fee:</strong> <span id="confirmPaymentFee"></span></p> <!-- Add this line -->
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
document.addEventListener('DOMContentLoaded', () => {
    // Set min date-time to now (rounded to nearest 5 mins)
    const now = new Date();
    const pad = n => n.toString().padStart(2, '0');
    const roundedNow = new Date(Math.ceil(now.getTime() / (1000 * 60 * 5)) * (1000 * 60 * 5));
    const iso = `${roundedNow.getFullYear()}-${pad(roundedNow.getMonth() + 1)}-${pad(roundedNow.getDate())}T${pad(roundedNow.getHours())}:${pad(roundedNow.getMinutes())}`;

    document.getElementById('start_date').min = iso;
    document.getElementById('end_date').min = iso;
});

function calculateFee() {
    const facility = document.getElementById('facility').value;
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    const feeDisplay = document.getElementById('payment-fee-display');

    if (!facility || !start || !end) {
        feeDisplay.textContent = 'Payment Fee: ';
        return;
    }

    // Use local time parsing to avoid timezone issues
    const startDate = parseLocalDateTime(start);
    const endDate = parseLocalDateTime(end);
    const now = new Date();

    if (!startDate || !endDate || endDate <= startDate) {
        feeDisplay.textContent = 'Payment Fee: ';
        document.getElementById('price_estimate').value = '';
        return;
    }

    let hours = (endDate - startDate) / (1000 * 60 * 60);

    function formatAMPM(date) {
        let h = date.getHours();
        let ampm = h >= 12 ? 'pm' : 'am';
        h = h % 12;
        if (h === 0) h = 12;
        return h + ampm;
    }

    function formatAMPMHour(date) {
        let h = date.getHours();
        let ampm = h >= 12 ? 'pm' : 'am';
        h = h % 12;
        if (h === 0) h = 12;
        return h + ampm;
    }

    const feeTable = {
        "6am-7am": 100, "6am-8am": 200, "6am-9am": 300, "6am-10am": 400, "6am-11am": 500, "6am-12pm": 600,
        "7am-8am": 100, "7am-9am": 200, "7am-10am": 300, "7am-11am": 400, "7am-12pm": 500, "7am-1pm": 600,
        "8am-9am": 100, "8am-10am": 200, "8am-11am": 300, "8am-12pm": 400, "8am-1pm": 500, "8am-2pm": 600,
        "9am-10am": 100, "9am-11am": 200, "9am-12pm": 300, "9am-1pm": 400, "9am-2pm": 500, "9am-3pm": 600,
        "10am-11am": 100, "10am-12pm": 200, "10am-1pm": 300, "10am-2pm": 400, "10am-3pm": 500, "10am-4pm": 600,
        "11am-12pm": 100, "11am-1pm": 200, "11am-2pm": 300, "11am-3pm": 400, "11am-4pm": 500, "11am-5pm": 600,
        "12pm-1pm": 100, "12pm-2pm": 200, "12pm-3pm": 300, "12pm-4pm": 400, "12pm-5pm": 500, "12pm-6pm": 600,
        "1pm-2pm": 100, "1pm-3pm": 200, "1pm-4pm": 300, "1pm-5pm": 400, "1pm-6pm": 500, "1pm-7pm": 800,
        "2pm-3pm": 100, "2pm-4pm": 200, "2pm-5pm": 300, "2pm-6pm": 400, "2pm-7pm": 700, "2pm-8pm": 1000,
        "3pm-4pm": 100, "3pm-5pm": 200, "3pm-6pm": 300, "3pm-7pm": 600, "3pm-8pm": 900, "3pm-9pm": 1200, "3pm-10pm": 1500,
        "4pm-5pm": 100, "4pm-6pm": 200, "4pm-7pm": 500, "4pm-8pm": 800, "4pm-9pm": 1100, "4pm-10pm": 1400,
        "5pm-6pm": 100, "5pm-7pm": 400, "5pm-8pm": 700, "5pm-9pm": 1000, "5pm-10pm": 1300,
        "6pm-7pm": 300, "6pm-8pm": 600, "6pm-9pm": 900, "6pm-10pm": 1200,
        "7pm-8pm": 300, "7pm-9pm": 600, "7pm-10pm": 900,
        "8pm-9pm": 300, "8pm-10pm": 600,
        "9pm-10pm": 300,

        // 30-minute slot keys
        "6:30am-7:30am": 100, "6:30am-8:30am": 200, "6:30am-9:30am": 300, "6:30am-10:30am": 400, "6:30am-11:30am": 500, "6:30am-12:30pm": 600,
        "7:30am-8:30am": 100, "7:30am-9:30am": 200, "7:30am-10:30am": 300, "7:30am-11:30am": 400, "7:30am-12:30pm": 500, "7:30am-1:30pm": 600,
        "8:30am-9:30am": 100, "8:30am-10:30am": 200, "8:30am-11:30am": 300, "8:30am-12:30pm": 400, "8:30am-1:30pm": 500, "8:30am-2:30pm": 600,
        "9:30am-10:30am": 100, "9:30am-11:30am": 200, "9:30am-12:30pm": 300, "9:30am-1:30pm": 400, "9:30am-2:30pm": 500, "9:30am-3:30pm": 600,
        "10:30am-11:30am": 100, "10:30am-12:30pm": 200, "10:30am-1:30pm": 300, "10:30am-2:30pm": 400, "10:30am-3:30pm": 500, "10:30am-4:30pm": 600,
        "11:30am-12:30pm": 100, "11:30am-1:30pm": 200, "11:30am-2:30pm": 300, "11:30am-3:30pm": 400, "11:30am-4:30pm": 500, "11:30am-5:30pm": 600,
        "12:30pm-1:30pm": 100, "12:30pm-2:30pm": 200, "12:30pm-3:30pm": 300, "12:30pm-4:30pm": 400, "12:30pm-5:30pm": 500, "12:30pm-6:30pm": 600,
        "1:30pm-2:30pm": 100, "1:30pm-3:30pm": 200, "1:30pm-4:30pm": 300, "1:30pm-5:30pm": 400, "1:30pm-6:30pm": 500, "1:30pm-7:30pm": 800,
        "2:30pm-3:30pm": 100, "2:30pm-4:30pm": 200, "2:30pm-5:30pm": 300, "2:30pm-6:30pm": 400, "2:30pm-7:30pm": 700, "2:30pm-8:30pm": 1000,
        "3:30pm-4:30pm": 100, "3:30pm-5:30pm": 200, "3:30pm-6:30pm": 300, "3:30pm-7:30pm": 600, "3:30pm-8:30pm": 900, "3:30pm-9:30pm": 1200, "3:30pm-10:30pm": 1500,
        "4:30pm-5:30pm": 100, "4:30pm-6:30pm": 200, "4:30pm-7:30pm": 500, "4:30pm-8:30pm": 800, "4:30pm-9:30pm": 1100, "4:30pm-10:30pm": 1400,
        "5:30pm-6:30pm": 100, "5:30pm-7:30pm": 400, "5:30pm-8:30pm": 700, "5:30pm-9:30pm": 1000, "5:30pm-10:30pm": 1300,
        "6:30pm-7:30pm": 300, "6:30pm-8:30pm": 600, "6:30pm-9:30pm": 900, "6:30pm-10:30pm": 1200,
        "7:30pm-8:30pm": 300, "7:30pm-9:30pm": 600, "7:30pm-10:30pm": 900,
        "8:30pm-9:30pm": 300, "8:30pm-10:30pm": 600,
        "9:30pm-10:30pm": 300
    };

    let fee = 0;
    switch (facility) {
        case 'Basketball Court':
        case 'Volleyball Court':
        case 'Badminton Court': {
            // Snap start to nearest hour down, end to nearest hour up
            let startHour = new Date(startDate);
            startHour.setMinutes(0, 0, 0);
            let endHour = new Date(endDate);
            if (endHour.getMinutes() !== 0 || endHour.getSeconds() !== 0 || endHour.getMilliseconds() !== 0) {
                endHour.setHours(endHour.getHours() + 1, 0, 0, 0);
            } else {
                endHour.setMinutes(0, 0, 0);
            }
            let startKey = formatAMPMHour(startHour);
            let endKey = formatAMPMHour(endHour);
            let key = `${startKey}-${endKey}`;
            if (feeTable[key] !== undefined) {
                fee = feeTable[key];
            } else {
                // Fallback: per-hour calculation
                let total = 0;
                let temp = new Date(startHour);
                while (temp < endHour) {
                    let hour = temp.getHours();
                    if (hour >= 6 && hour < 18) total += 100;
                    else if (hour >= 18 && hour < 22) total += 300;
                    temp.setHours(temp.getHours() + 1);
                }
                fee = total;
            }
            break;
        }
        case 'Whole Court': {
            let wcDay = 0, wcNight = 0;
            for (let i = 0; i < hours; i++) {
                let h = new Date(startDate.getTime() + i * 3600000);
                if (h.getHours() >= 18) wcNight++;
                else wcDay++;
            }
            fee = (wcDay * 200) + (wcNight * 600);
            break;
        }
        case 'Bulwagan with Aircon':
            fee = hours <= 4 ? 5000 : 5000 + Math.ceil(hours - 4) * 1000;
            break;
        case 'Bulwagan without Aircon':
            fee = hours <= 4 ? 3500 : 3500 + Math.ceil(hours - 4) * 700;
            break;
        case 'Small Meeting Room':
            fee = Math.ceil(hours) * 200;
            break;
        case 'Session Hall':
            fee = Math.ceil(hours) * 600;
            break;
        case 'Conference Room':
            fee = Math.ceil(hours) * 400;
            break;
        case 'Community Center with Aircon':
            fee = hours <= 4 ? 4000 : 4000 + Math.ceil(hours - 4) * 800;
            break;
        case 'Community Center without Aircon':
            fee = hours <= 4 ? 3000 : 3000 + Math.ceil(hours - 4) * 600;
            break;
        case 'Parking':
        case 'Playground':
            fee = 'To be determined';
            break;
        default:
            fee = 'N/A';
    }

    feeDisplay.textContent = 'Payment Fee: ' + (typeof fee === 'number' ? `₱${fee}` : fee);
    document.getElementById('price_estimate').value = typeof fee === 'number' ? fee : '';
}

function getCurrentFeeText() {
    return document.getElementById('payment-fee-display').textContent.replace('Payment Fee: ', '');
}

document.addEventListener('DOMContentLoaded', function () {
    const reservationForm = document.getElementById('reservationForm');
    const confirmFacility = document.getElementById('confirmFacility');
    const confirmStartDate = document.getElementById('confirmStartDate');
    const confirmEndDate = document.getElementById('confirmEndDate');
    const confirmReason = document.getElementById('confirmReason');
    const confirmAdditionalNote = document.getElementById('confirmAdditionalNote');
    const confirmPaymentFee = document.getElementById('confirmPaymentFee');

    ['facility', 'start_date', 'end_date'].forEach(id => {
        document.getElementById(id).addEventListener('change', calculateFee);
    });

    reservationForm.addEventListener('change', function () {
        confirmFacility.textContent = document.getElementById('facility').value;
        confirmStartDate.textContent = document.getElementById('start_date').value;
        confirmEndDate.textContent = document.getElementById('end_date').value;
        confirmReason.textContent = document.getElementById('reason').value;
        confirmAdditionalNote.textContent = document.getElementById('additional_note').value;
    });

    document.getElementById('showConfirmationBtn').addEventListener('click', function () {
        const reservationForm = document.getElementById('reservationForm');
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        // Prevent modal if start or end date is empty
        if (!start || !end) {
            reservationForm.reportValidity(); // Show browser validation
            return;
        }

        // Update modal details
        document.getElementById('confirmFacility').textContent = document.getElementById('facility').value;
        document.getElementById('confirmStartDate').textContent = start;
        document.getElementById('confirmEndDate').textContent = end;
        document.getElementById('confirmReason').textContent = document.getElementById('reason').value;
        document.getElementById('confirmAdditionalNote').textContent = document.getElementById('additional_note').value;
        document.getElementById('confirmPaymentFee').textContent = getCurrentFeeText();

        // Show the modal
        $('#confirmationModal').modal('show');
    });

    reservationForm.addEventListener('submit', function (event) {
        if (!document.getElementById('confirmCheckbox').checked) {
            event.preventDefault();
            alert('Please confirm the details before submitting.');
        }
    });

    calculateFee();
});

function parseLocalDateTime(str) {
    // str is "YYYY-MM-DDTHH:MM"
    const [date, time] = str.split('T');
    const [year, month, day] = date.split('-').map(Number);
    const [hour, minute] = time.split(':').map(Number);
    return new Date(year, month - 1, day, hour, minute);
}
</script>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> <!-- `modal-lg` for larger content -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <h2>BARANGAY BLUE RIDGE B – TERMS AND CONDITIONS FOR FACILITY RESERVATION</h2>

<h3>I. GENERAL TERMS</h3>
<strong>Reservation Policy</strong>
<ul>
    <li>All reservations are on a first-come, first-served basis.</li>
    <li>Full payment is required at least seven (7) days before the event to confirm a reservation.</li>
    <li>For check payments, please issue to Bgy. Blue Ridge B.</li>
</ul>
<strong>No Pay, No Play</strong>
<p>No access will be granted without full advance payment.</p>
<strong>Rates and Schedule</strong>
<ul>
    <li>Operating Hours: Monday to Sunday, 8:00 AM to 10:00 PM</li>
    <li>Maximum duration for any event is until 12:00 midnight.</li>
    <li>Music and noise levels must be reduced by 11:00 PM.</li>
</ul>
<strong>Cancellation Policy</strong>
<ul>
    <li>20% penalty applies for cancellations made at least 7 days before the event.</li>
    <li>No refund for cancellations made 6 days or less before the event.</li>
</ul>

<h3>II. FACILITY USAGE FEES</h3>
<p><strong>A. Sports Courts (Court A – Basketball/Volleyball; Court B – Badminton)</strong></p>
<ul>
    <li>Court A: ₱100/hr (Day), ₱300/hr (Night)</li>
    <li>Court B: ₱100/hr (Day), ₱300/hr (Night)</li>
    <li>Whole Court: ₱200/hr (Day), ₱600/hr (Night)</li>
    <li>Note: Only BBRB employees are allowed to remove sports equipment.</li>
</ul>
<p><strong>B. Major Events (Above 30 pax) in Sports Courts</strong></p>
<ul>
    <li>First 4 hours: ₱4,000.00</li>
    <li>Additional hour: ₱1,000.00/hr</li>
    <li>Cash bond: ₱1,000.00</li>
    <li>Security: ₱300.00/BPSO</li>
    <li>Cleaning: ₱200.00</li>
    <li>Power Supply: ₱100.00/hr</li>
</ul>
<p><strong>C. Multi-Purpose Hall (Bulwagan)</strong></p>
<ul>
    <li>₱5,000.00 for 4 hours (with aircon)</li>
    <li>₱3,500.00 for 4 hours (without aircon)</li>
    <li>₱1,000.00/hr (additional with aircon)</li>
    <li>₱700.00/hr (additional without aircon)</li>
</ul>
<p><strong>D. Meeting Rooms (with aircon)</strong></p>
<ul>
    <li>Session Hall: ₱600.00/hr</li>
    <li>Conference Room: ₱400.00/hr</li>
    <li>Small Meeting Room: ₱200.00/hr</li>
</ul>
<p><strong>E. Community Center</strong></p>
<ul>
    <li>Street Level: ₱4,000.00 (4 hrs with aircon), ₱3,000.00 (4 hrs without aircon)</li>
    <li>₱800.00/hr (addl. with aircon), ₱600.00/hr (addl. without aircon)</li>
    <li>Rooftop: ₱600.00/hr</li>
</ul>

<h3>III. RENTALS AND ADDITIONAL SERVICES</h3>
<ul>
    <li>Sound System: ₱1,000.00</li>
    <li>Projector w/ Screen: ₱1,500.00</li>
    <li>Life Time Table: ₱150.00 each</li>
    <li>Life Time Chair: ₱50.00 each</li>
    <li>Long Table: ₱200.00 each</li>
    <li>Monoblock Chair: ₱10.00 each</li>
    <li>Power Supply (Fan): ₱100.00/hr</li>
    <li>Sound System Operator: ₱100.00 (setup only)</li>
    <li>Security: ₱250.00 (1–50 pax), ₱500.00 (51+ pax)</li>
    <li>Post-Event Cleaning: ₱250.00</li>
</ul>

<h3>IV. GUIDELINES AND PROHIBITIONS</h3>
<ul>
    <li>Observe proper decorum, cleanliness, and sportsmanship at all times.</li>
    <li>Athletic attire/footwear required. No slippers/sandals/barefoot.</li>
    <li>No pets, bicycles, scooters inside.</li>
    <li>No littering or spitting. Use trash bins.</li>
    <li>Strictly no alcohol, illegal drugs, or intoxication.</li>
    <li>No deadly weapons or firecrackers allowed.</li>
    <li>No cooking/heating food or washing dishes.</li>
    <li>Damages deducted from cash bond.</li>
</ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


</body>

</html>