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
    $gender = "";
    $name = "";
    $lastName = "";
    $class = "";
    $private_no = "";
    $noti = "";
    
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
    
    //Get Gender
    if (strpos($name, 'เด็กชาย') !== false || strpos($name, 'นาย') !== false) {
        $gender = 'ชาย';
    } elseif (strpos($name, 'เด็กหญิง') !== false || strpos($name, 'นางสาว') !== false || strpos($name, 'นาง') !== false) {
        $gender = 'หญิง';
    }

    //rulse
    if ($found == false) {
        // Return a response to the JavaScript
        $status = "failure";
        echo json_encode(array("status" => $status));
        exit;
    }
    if ($room != "123" && $room != "125" && $room != "126" && $room != "116") {
        // Return a response to the JavaScript
        $status = "success";
        $noti = "ไม่พบเลขห้องปฏิบัติการ ลองใหม่อีกครั้งคุณ";
        echo json_encode(array("status" => $status, "noti" => $noti, "name" => $name));
        exit;
    }

    //Check id number in form
    $idInForm = false;
    for ($no = 0; $no < count($formData); $no++) {
        if ($formData[$no]['ID Number'] == $idNumber && $formData[$no]['Out'] == null) {
            //Long time check

            //Get Date
            $currentDate = date('d-m-Y');
            $inDate = $formData[$no]['Date'];
            
            $currentDateArray = explode("-", $currentDate);
            $inDateArray = explode("-", $inDate);
            
            $day = $currentDateArray[0] - $inDateArray[0];
            $month = $currentDateArray[1] - $inDateArray[1];
            $year = $currentDateArray[2] - $inDateArray[2];

            //Get Time
            $currentTime = date('H:i:s');
            $inTime = $formData[$no]['In'];

            $currentTimeArray = explode(":", $currentTime);
            $inTimeArray = explode(":", $inTime);

            $hour = $currentTimeArray[0] - $inTimeArray[0];

            // if this have checked-in and don't long time
            if ($hour < 6 && $day == 0 && $month == 0 && $year == 0) {
                $idInForm = true;
                $formData[$no]['Out'] = date('H:i:s');
                $formData[$no]['Note'] = $note;
                $status = "success";
                $noti = "ลงชื่อออกสำเร็จ";
                break;
            }
        }
    }

    //if never have name in form or long time
    if (!$idInForm) {
        //Register in form
        $registData = array(
            "No." => count($formData) + 1,
            "Date" => date('d-m-Y'),
            "ID Number" => $formIdnumber,
            "Name" => $name,
            "Lastname" => $lastName,
            "Class" => $class,
            "Gender" => $gender,
            "In" => date('H:i:s'),
            "Out" => null,
            "Lab Room" => $room,
            "Note" => $note
        );
        $formData[] = $registData;
        $status = "success";
        $noti = "ลงชื่อเข้าสำเร็จ อย่าลืมกลับมาลงทะเบียนออก";
    }

    
    //Sort the data based on the "No." field
    usort($formData, function($a, $b) {
        return $a['No.'] <=> $b['No.'];
    });
    
    // Convert the updated array back into a JSON string with UTF-8 encoding
    $jsonForm = json_encode($formData, JSON_UNESCAPED_UNICODE);

    // Save the updated JSON data to the file
    file_put_contents('form.json', $jsonForm);
    
    // Return a response to the JavaScript
    echo json_encode(array("status" => $status, "name" => $name, "noti" => $noti));
?>