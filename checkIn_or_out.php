<?php
    // Get the ID number and on_check value sent by the user
    $idNumber = $_POST['idNumber'];

    // Read the JSON files
    if (!file_exists('form.json')) {
        echo json_encode(array("status" => "failure", "message" => "Error: form.json file not found"));
        exit();
    }
    if (!file_exists('Students_data.json')) {
        echo json_encode(array("status" => "failure", "message" => "Error: Students_data.json file not found"));
        exit();
    }

    $jsonForm = file_get_contents('form.json');
    $jsonStudentsData = file_get_contents('Students_data.json');

    // Convert the JSON data into a PHP array
    $formData = json_decode($jsonForm, True);
    $stdsdata = json_decode($jsonStudentsData, true);

    // Initialize default variables for response
    $status = "failure";
    $found = false;
    $formIdnumber = "";
    $gender = "";
    $name = "";
    $lastName = "";
    $class = "";
    $private_no = "";

    // Loop through the array to find student data in Students_data.json
    for ($i = 0; $i < count($stdsdata); $i++) {
        // Check for a match with the ID number
        if ($stdsdata[$i]['ID Number'] == $idNumber) {
            $found = true;
            //Get Students Data from Students_data.json
            $formIdnumber = $stdsdata[$i]['ID Number'];
            $name = $stdsdata[$i]["Name"];
            $lastName = $stdsdata[$i]["LastName"];
            $class = $stdsdata[$i]["Class"];
            $private_no = $stdsdata[$i]["PrivateNo."];
            break;
        }
    }

    // Check if the student was found in Students_data.json
    if (!$found) {
        echo json_encode(array("status" => "failure", "message" => "Error: Student not found in Students_data.json"));
        exit();
    }
    $idInForm = "in";

    // Check if the student is in form.json
    for ($no = count($formData) - 1; $no >= 0; $no--) {
        if ($formData[$no]['ID Number'] == $idNumber && $formData[$no]['Out'] == null) {
            // Check if student has checked-in for more than 6 hours
            $currentDate = date('d-m-Y');
            $inDate = $formData[$no]['Date'];

            $currentDateArray = explode("-", $currentDate);
            $inDateArray = explode("-", $inDate);

            $day = $currentDateArray[0] - $inDateArray[0];
            $month = $currentDateArray[1] - $inDateArray[1];
            $year = $currentDateArray[2] - $inDateArray[2];

            $currentTime = date('H:i:s');
            $inTime = $formData[$no]['In'];

            $currentTimeArray = explode(":", $currentTime);
            $inTimeArray = explode(":", $inTime);

            $hour = $currentTimeArray[0] - $inTimeArray[0];

            if ($hour < 6 && $day == 0 && $month == 0 && $year == 0) {
                $idInForm = "out";
            }
            // Return a response to the JavaScript
            echo json_encode(array("status" => "success", "idInForm" => $idInForm));
            exit();
        }
    }

    // Return a response to the JavaScript
    echo json_encode(array("status" => "success", "idInForm" => $idInForm));
    exit();

?>