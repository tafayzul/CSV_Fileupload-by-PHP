<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "internshala";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a CSV file is uploaded
if (isset($_FILES["csv_file"])) {
    $file = $_FILES["csv_file"]["tmp_name"];

    // Read the CSV file
    if (($handle = fopen($file, "r")) !== false) {
        $totalRecords = 0;
        $loadedRecords = 0;

        // Count the total number of records
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $totalRecords++;
        }

        // Reset the file pointer to the beginning of the file
        rewind($handle);

        // Load data in the background
        set_time_limit(0); // Disable script execution time limit

        // Process the CSV file line by line
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Insert the record into the database
            $stmt = $conn->prepare("INSERT INTO physicians (Title, First_Name, Last_Name, Full_Name, Gender, Specialty, Practice, Phone, Fax, Email, Address, City, County, State, Zip, Latitude, Longitude, SIC_Code, Website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $title = $data[0];
            $firstName = $data[1];
            $lastName = $data[2];
            $fullName = $data[3];
            $gender = $data[4];
            $specialty = $data[5];
            $practice = $data[6];
            $phone = $data[7];
            $fax = $data[8];
            $email = $data[9];
            $address = $data[10];
            $city = $data[11];
            $county = $data[12];
            $state = $data[13];
            $zip = $data[14];
            $latitude = $data[15];
            $longitude = $data[16];
            $sicCode = $data[17];
            $website = $data[18];

            $stmt->bind_param("sssssssssssssssssss", $title, $firstName, $lastName, $fullName, $gender, $specialty, $practice, $phone, $fax, $email, $address, $city, $county, $state, $zip, $latitude, $longitude, $sicCode, $website);

            if ($stmt->execute()) {
                // Update the loaded records count
                $loadedRecords++;

                // Send a response to the browser
                if (ob_get_level() > 0) {
                    ob_end_flush();
                }
                echo "Loaded $loadedRecords records out of $totalRecords. Please do not close this page.";
                echo "<br>";

                // Flush the output buffer and send the response to the browser
                ob_start(); // Start output buffering
                @ob_flush();
                flush();

                
                usleep(10000); 
            } else {
                // Handle the error here (e.g., log the error, display an error message, etc.)
                echo "Error inserting record: " . $stmt->error;
            }
        }

        fclose($handle);

        echo "All records loaded successfully!";
    } else {
        echo "Failed to open the CSV file.";
    }
}

$conn->close();

?>

