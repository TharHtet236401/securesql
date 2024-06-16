<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medi_protect_db";
$port = "3308";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient_id = isset($_GET['id']) ? $_GET['id'] : '';

$patient_info = '';

if ($patient_id) {
    // Validate that the patient_id is an integer
    if (filter_var($patient_id, FILTER_VALIDATE_INT)) {
        // Prepare a statement
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        if ($stmt === false) {
            // Log error
            error_log('mysqli prepare() failed: ' . htmlspecialchars($conn->error));
            die('Internal server error');
        }
        $stmt->bind_param("i", $patient_id);
        
        // Execute the statement
        if ($stmt->execute() === false) {
            // Log error
            error_log('mysqli execute() failed: ' . htmlspecialchars($stmt->error));
            die('Internal server error');
        }

        // Get the result
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patient_info .= "<div class='patient-record'>";
                $patient_info .= "<p><strong>Name:</strong> " . htmlspecialchars($row["name"]) . "</p>";
                $patient_info .= "<p><strong>Age:</strong> " . htmlspecialchars($row["age"]) . "</p>";
                $patient_info .= "<p><strong>Diagnosis:</strong> " . htmlspecialchars($row["diagnosis"]) . "</p>";
                $patient_info .= "<p><strong>Contact Info:</strong> " . htmlspecialchars($row["contact_info"]) . "</p>";
                $patient_info .= "</div>";
            }
        } else {
            $patient_info = "No patient found with ID: " . htmlspecialchars($patient_id);
        }

        // Close the statement
        $stmt->close();
    } else {
        $patient_info = "Invalid patient ID format.";
    }
} else {
    $patient_info = "Enter a patient ID to view details.";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Healthcare Portal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <img src="logo3.png" class="logo" alt="sdf">
            <ul class="navbar-nav">
                <li><a href="#">Home</a></li>
                <li><a href="#">Patients</a></li>
                <li><a href="#">Appointments</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Secure Healthcare Portal</h1>
        <form method="get" action="">
            <div>
                <label for="id">Patient ID:</label>
                <input type="text" id="id" name="id" required>
                <input type="submit" value="View Patient">
            </div>
        </form>
        <div class="patient-info">
            <h2>Patient Information</h2>
            <p><?php echo $patient_info; ?></p>
        </div>
    </div>
</body>
</html>
