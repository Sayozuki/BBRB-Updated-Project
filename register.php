<?php
require 'db.php';

$errors = []; // Array to store error messages
$registrationSuccess = false; // Flag for successful registration

// Serve address list as JSON if requested
if (isset($_GET['get_addresses'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT id, address_name FROM addresses ORDER BY address_name ASC");
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($addresses);
    exit;
}

// Serve location data (limited to Quezon City) as JSON
if (isset($_GET['get_location_data'])) {
    header('Content-Type: application/json');
    $data = [
        'cities' => [
            "Quezon City", "Marikina City"
        ],
    ];
    echo json_encode($data);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip_code = $_POST['zip_code'];
    $birthday = $_POST['birthday'];
    $blood_type = $_POST['blood_type'];
    $sss_number = $_POST['sss_number'];
    $pagibig_number = $_POST['pagibig_number'];
    $tin_number = $_POST['tin_number'];

    // Validate password length
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    // Validate contact number
    if (!preg_match('/^\d{11}$/', $contact_number)) {
        $errors[] = 'Contact number must be 11 digits and contain only numbers.';
    }

    // Validate age (at least 13 years old)
    $birthDate = new DateTime($birthday);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    if ($age < 13) {
        $errors[] = 'You must be at least 13 years old to register.';
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password and insert into the database
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (
            first_name, middle_name, last_name, email, password, contact_number,
            address, city, zip_code, birthday, blood_type, sss_number,
            pagibig_number, tin_number
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $hashed_password,
            $contact_number,
            $address,
            $city,
            $zip_code,
            $birthday,
            $blood_type,
            $sss_number,
            $pagibig_number,
            $tin_number
        ]);

        $registrationSuccess = true; // Set the flag to true
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="mt-3">
                                <?php foreach ($errors as $error): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                <form method="POST">
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Enter your middle name">
                </div>
                <div class="form-group mb-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <small id="passwordError" class="text-danger"></small> <!-- Error message -->
                </div>
                <div class="form-group mb-3">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    <small id="confirmPasswordError" class="text-danger"></small> <!-- Error message -->
                </div>
                <div class="form-group mb-3">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control"
                        placeholder="Enter your contact number" required
                        pattern="\d{11}" maxlength="11"
                        title="Contact Number must be 11 digits and contain only numbers.">
                    <small id="contactNumberError" class="text-danger"></small> <!-- Error message -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="address">Street</label>
                    <select name="address" id="address" class="form-control" required>
                        <option value="">Select Address</option>
                        <option value="Comets Loop">Comets Loop</option>
                        <option value="Crest Line Street">Crest Line Street</option>
                        <option value="Evening Glow Road">Evening Glow Road</option>
                        <option value="Highland Drive">Highland Drive</option>
                        <option value="Hillside Drive">Hillside Drive</option>
                        <option value="Milkyway Drive">Milkyway Drive</option>
                        <option value="Orion Street">Orion Street</option>
                        <option value="Polaris Street">Polaris Street</option>
                        <option value="Starlight Loop">Starlight Loop</option>
                        <option value="Sunset Drive">Sunset Drive</option>
                        <option value="Twilight Court">Twilight Court</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="city">City</label>
                    <select name="city" id="city" class="form-control" required>
                        <option value="">Select City</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="zip_code">ZIP Code</label>
                    <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="Enter your ZIP code" required pattern="\d*" title="Only numbers are allowed">
                </div>
                <div class="form-group mb-3">
                    <label for="birthday">Birthday</label>
                    <input type="date" name="birthday" id="birthday" class="form-control" required>
                    <small id="birthdayError" class="text-danger"></small> <!-- Error message -->
                </div>
                <div class="form-group mb-3">
                    <label for="blood_type">Blood Type</label>
                    <select name="blood_type" id="blood_type" class="form-control">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="sss_number">SSS Number</label>
                    <input type="text" name="sss_number" id="sss_number" class="form-control"
                        placeholder="Enter your SSS number"
                        pattern="\d{14}" maxlength="14"
                        title="SSS Number must be 14 digits and contain only numbers.">
                </div>
                <div class="form-group mb-3">
                    <label for="pagibig_number">Pag-ibig Number</label>
                    <input type="text" name="pagibig_number" id="pagibig_number" class="form-control"
                        placeholder="Enter your Pag-ibig number"
                        pattern="\d{12}" maxlength="12"
                        title="Pag-ibig Number must be 12 digits and contain only numbers.">
                </div>
                <div class="form-group mb-3">
                    <label for="tin_number">TIN Number</label>
                    <input type="text" name="tin_number" id="tin_number" class="form-control"
                        placeholder="Enter your TIN number"
                        pattern="\d{12}" maxlength="12"
                        title="TIN Number must be 12 digits and contain only numbers.">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your registration was successful! You will be redirected to the login page shortly.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch and populate regions, provinces, cities, barangays
    fetch('register.php?get_location_data=1')
        .then(response => response.json())
        .then(data => {
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');


            data.cities.forEach(function(city) {
                const option = document.createElement('option');
                option.value = city;
                option.text = city;
                citySelect.appendChild(option);
            });

            // Initialize Select2 after options are loaded
            $('#city').select2({ placeholder: "Select City", width: '100%' });
        });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const contactNumberInput = document.getElementById('contact_number');
    const birthdayInput = document.getElementById('birthday');

    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    const contactNumberError = document.getElementById('contactNumberError');
    const birthdayError = document.getElementById('birthdayError');

    // Validate password length
    passwordInput.addEventListener('input', function () {
        if (passwordInput.value.length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters long.';
        } else {
            passwordError.textContent = '';
        }
    });

    // Validate confirm password matches
    confirmPasswordInput.addEventListener('input', function () {
        if (confirmPasswordInput.value !== passwordInput.value) {
            confirmPasswordError.textContent = 'Passwords do not match.';
        } else {
            confirmPasswordError.textContent = '';
        }
    });

    // Validate contact number format
    contactNumberInput.addEventListener('input', function () {
        const contactNumberRegex = /^\d{11}$/;
        if (!contactNumberRegex.test(contactNumberInput.value)) {
            contactNumberError.textContent = 'Contact number must be 11 digits and contain only numbers.';
        } else {
            contactNumberError.textContent = '';
        }
    });

    // Validate age (at least 13 years old)
    birthdayInput.addEventListener('change', function () {
        const birthDate = new Date(birthdayInput.value);
        const currentDate = new Date();
        const age = currentDate.getFullYear() - birthDate.getFullYear();
        const monthDiff = currentDate.getMonth() - birthDate.getMonth();
        const dayDiff = currentDate.getDate() - birthDate.getDate();

        if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
            age--;
        }

        if (age < 13) {
            birthdayError.textContent = 'You must be at least 13 years old to register.';
        } else {
            birthdayError.textContent = '';
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($registrationSuccess): ?>
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        setTimeout(function () {
            window.location.href = "login.php";
        }, 3000); // Redirect after 3 seconds
    <?php endif; ?>
});
</script>
</body>

</html>