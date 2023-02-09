<?php
    // Get the ID number and on_check value sent by the user
    $idNumber = $_POST['idNumber'];
    $note = $_POST['note'];
    
    // Read the JSON file
    $jsonForm = file_get_contents('form.json');
    $jsonStudentsData = file_get_contents('Students_data.json');

    // Convert the JSON data into a PHP array
    $formData = json_decode($jsonForm, True);
    $stdsdata = json_decode($jsonStudentsData, true);
    
    // Initialize default variables for response
    $status = "failure";
    $name = "";
    $lastName = "";
    $class = "";
    $private_no = "";
    
    // Loop through the array to find student data in Students_data.json
    for ($i = 0; $i < count($stdsdata); $i++) {
        // Check for a match with the ID number
        if ($stdsdata[$i]['ID Number'] == $idNumber) {
            //Get Students Data from Students_data.json
            $status = "success";
            $name = $stdsdata[$i]["Name"];
            $lastName = $stdsdata[$i]["LastName"];
            $class = $stdsdata[$i]["Class"];
            $private_no = $stdsdata[$i]["PrivateNo."];

            //Register in form
            $registData = array(
                "No." => count($formData) + 1,
                "Date" => date('Y-m-d'),
                "Name" => $name,
                "Lastname" => $lastName,
                "Class" => $class,
                "In" => null,
                "Out" => null,
                "Note" => $note
              );
              $formData[] = $registData;
              
        //       //Sort the data based on the "No." field
        //     //   usort($formData, function($a, $b) {
        //     //       return $a['No.'] <=> $b['No.'];
        //     //   });
            break;
        }
    }
    
    // Convert the updated array back into a JSON string
    $jsonForm = json_encode($formData);
    
    // Save the updated JSON data to the file
    file_put_contents('form.json', $jsonForm);
    
    // Return a response to the JavaScript
    echo json_encode(array("status" => $status, "name" => $name));
    exec('python converter.py');
?>