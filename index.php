<?php
    // Get the ID number and on_check value sent by the user
    $idNumber = $_POST['idNumber'];
    $note = $_POST['note'];
    $room = $_POST['room'];
    
    // Read the JSON file
    $jsonForm = file_get_contents('form.json');
    $jsonStudentsData = file_get_contents('Students_data.json');

    // Convert the JSON data into a PHP array
    $formData = json_decode($jsonForm, True);
    $stdsdata = json_decode($jsonStudentsData, true);
    
    // Initialize default variables for response
    $status = "failure";
    $found = false;
    $formIdnumber = "";
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
    if ($found == false) {
        // Return a response to the JavaScript
        $status = "failure";
        echo json_encode(array("status" => $status));
        exit;
    }
    //Check id number in form
    $idInForm = false;
    for ($no = 0; $no < count($formData); $no++) {
        if ($formData[$no]['ID Number'] == $idNumber && $formData[$no]['Out'] == null) {
            $idInForm = true;
            //Out register in form
            $formData[$no]['Out'] = date('H:i:s');
            $formData[$no]['Note'] = $note;
            $status = "success";
            break;
        }
    }

    if (!$idInForm) {
        //Register in form
        $registData = array(
            "No." => count($formData) + 1,
            "Date" => date('d-m-Y'),
            "Name" => $name,
            "ID Number" => $formIdnumber,
            "Lastname" => $lastName,
            "Class" => $class,
            "In" => date('H:i:s'),
            "Out" => null,
            "Lab Room" => $room,
            "Note" => $note
        );
        $formData[] = $registData;
        $status = "success";
    }

    
    //Sort the data based on the "No." field
    usort($formData, function($a, $b) {
        return $a['No.'] <=> $b['No.'];
    });
    
    // Convert the updated array back into a JSON string
    $jsonForm = json_encode($formData);
    
    // Save the updated JSON data to the file
    file_put_contents('form.json', $jsonForm);
    
    // Return a response to the JavaScript
    echo json_encode(array("status" => $status, "name" => $name));
    exec('python converter.py');
?>