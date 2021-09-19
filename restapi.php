<?php
require_once('dbConnect.php');

function signUp($doc) {
    global $conn;
    if(mysqli_query($conn,$doc))
    return true;
}
if(isset($_GET['getBooks'])) {

    $sql = "SELECT * FROM books";
    $result = $conn->query($sql);
    $rows = array();
    $check_sql = "SELECT ref_no FROM orders";
    $result_check = $conn->query($check_sql);
    $ref_nos = array();
    while ($r = mysqli_fetch_assoc($result_check)) {
        $ref_nos[] = $r['ref_no'];
    }
while($r = mysqli_fetch_assoc($result)) {
    if (in_array($r['ref_no'], $ref_nos))
    {
        $r['status'] = 'Not Available';
    }
    else {
        $r['status'] = 'Available';
    }
    
    $rows[] = $r;
}
    echo json_encode($rows);
}
if(isset($_GET['addBook'])) {
    $refno = $_POST['ref_no'];
    $title = $_POST['title'];
    $authors = $_POST['authors'];
    $type = $_POST['type'];

    $sql = "INSERT INTO books(`ref_no`,`title`,`authors`,`type`) VALUES ($refno, '$title', '$authors', '$type')";
    if(signUp($sql)){
        echo 'Success';
    }
    else {
        echo 'Failure';
    }
}
if(isset($_GET['editBook'])) {
    $refno = $_POST['ref_no'];
    $title = $_POST['title'];
    $authors = $_POST['authors'];
    $type = $_POST['type'];
    $sql = "UPDATE books SET title = '$title', authors = '$authors', type = '$type' WHERE ref_no = $refno";
    if(signUp($sql)){
        echo 'Success';
    }
    else {
        echo 'Failure';
    }
}
if(isset($_GET['editUser'])) {
    $regno = $_POST['reg_no'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $sql = "UPDATE students SET name = '$name', contact = $contact WHERE reg_no = $regno";
    if(signUp($sql)){
        echo 'Success';
    }
    else {
        echo 'Failure';
    }
}
if(isset($_GET['deleteUser'])) {
    $regno = $_POST['reg_no'];
    $sql = "DELETE FROM students WHERE reg_no = $regno";
    if(signUp($sql)){
        echo 'Success';
    }
    else {
        echo 'Failure';
    }
}
if(isset($_GET['deleteBook'])) {
    $refno = $_POST['ref_no'];
    $sql = "DELETE FROM books WHERE ref_no = $refno";
    if(signUp($sql)){
        echo 'Success';
    }
    else {
        echo 'Failure';
    }
}
if(isset($_GET['search'])) {
    $regno = $_POST['query'];
    $sql = "SELECT reg_no FROM students WHERE reg_no LIKE '%$regno%' LIMIT 5";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        while($row=$result->fetch_assoc()) {
            echo '<a href="#" class="list-group-item list-group-item-action">'.$row['reg_no'].'</a>';
        }
    }
    else {
        echo '<p class="list-group-item border-1">No Records</p>';
    }
}
if(isset($_GET['getUser'])) {
    $regno = $_POST['reg_no'];
    $sql = "SELECT * FROM students WHERE reg_no = $regno";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo json_encode($row);
}
if(isset($_GET['checkout'])) {
    $requestPayload = file_get_contents("php://input");
    $objects = json_decode($requestPayload,true);

    foreach($objects as $object) {
        $curr_date = date("Y-m-d");
        $reg_no = $object['reg_no'];
        $ref_no = $object['ref_no'];
        $due_date = date('Y-m-d', strtotime($curr_date . " + ". $object['days'] ." day"));
        $sql = "INSERT INTO orders(`reg_no`,`ref_no`,`buy_date`,`due_date`) VALUES($reg_no, $ref_no, '$curr_date', '$due_date')";
        if(signUp($sql)) {
            echo 'Success';
        }
        else {
            echo 'Failure';
        }
    }

}
if(isset($_GET['addUser'])) {
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $sql = "INSERT INTO students(`reg_no`,`name`,`contact`) VALUES ($reg_no, '$name', $contact)";
    if (signUp($sql)) {
        echo 'Success';
    }
    else {
        echo ' Failure';
    }
}
if(isset($_GET['getUsers'])) {
    $sql = "SELECT * FROM students";
    $result = $conn->query($sql);
    $rows = array();
    while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
}
if(isset($_GET['getUserBooks'])) {
    $reg_no = $_POST['reg_no'];
    $sql = "SELECT * FROM orders INNER JOIN books ON orders.ref_no = books.ref_no WHERE orders.reg_no = $reg_no";
    $result = $conn->query($sql);
    $rows = array();
    $curr_date = date("Y-m-d");
    $date1 = new DateTime($curr_date);

    while($r = mysqli_fetch_assoc($result)) {
        $date2 = new DateTime($r['due_date']);
        $days_remaining  = $date2->diff($date1)->format('%a');
        $date1_t =  new DateTime($r['buy_date']);
        $date2_t =  new DateTime($r['due_date']);
        $days  = $date2_t->diff($date1_t)->format('%a'); 
        if ($days_remaining <= $days) {
            $r['days'] = $days_remaining. ' Days Left';
        }
        else {
            $r['days'] = $days_remaining. ' Days Overdue';
        }
        $r['status'] = 'Ongoing' ;
        $rows[] = $r;
    }
    $sql = "SELECT * FROM history INNER JOIN books ON history.ref_no = books.ref_no WHERE history.reg_no = $reg_no";
    $result = $conn->query($sql);
    while($r = mysqli_fetch_assoc($result)) {
        $date2 = new DateTime($r['due_date']);
        $days_remaining  = $date2->diff($date1)->format('%a');
        $date1_t =  new DateTime($r['buy_date']);
        $date2_t =  new DateTime($r['due_date']);
        $days  = $date2_t->diff($date1_t)->format('%a'); 
        if ($days_remaining <= $days) {
            $r['days'] = $days_remaining. ' Days Left';
        }
        else {
            $r['days'] = $days_remaining. ' Days Overdue';
        }
        $rows[] = $r;
    }
    echo json_encode($rows);

}
if(isset($_GET['renew'])) {
    $ref_no = $_POST['ref_no'];
    $reg_no = $_POST['reg_no'];
    $sql = "SELECT * FROM orders WHERE reg_no = $reg_no AND ref_no = $ref_no";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $buy_date = $row['buy_date'];
    $due_date = $row['due_date'];
    $curr_date = date("Y-m-d");
    $date1 = new DateTime($buy_date);
$date2 = new DateTime($due_date);
$days  = $date2->diff($date1)->format('%a');
$new_due_date = date('Y-m-d', strtotime($curr_date . " + ". $days ." day"));
    $sql = "INSERT INTO history(`ref_no`,`reg_no`,`status`, `buy_date`,`due_date`) VALUES($ref_no, $reg_no , 'Renewed', '$buy_date', '$due_date')";
    if(signUp($sql)) {
        $sql = "DELETE FROM orders WHERE ref_no = $ref_no";
        if(signUp($sql )) {
            $sql = "INSERT INTO orders(`reg_no`,`ref_no`,`buy_date`,`due_date`) VALUES($reg_no, $ref_no, '$curr_date', '$new_due_date')";
        if(signUp($sql)) {
            echo 'Success';
        }
        else {
            echo 'Failure';
        }
        }
    }
}
if(isset($_GET['return'])) {
    $ref_no = $_POST['ref_no'];
    $reg_no = $_POST['reg_no'];
    $sql = "SELECT * FROM orders WHERE reg_no = $reg_no AND ref_no = $ref_no";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $buy_date = $row['buy_date'];
    $due_date = $row['due_date'];
    $sql = "INSERT INTO history(`ref_no`,`reg_no`,`status`, `buy_date`,`due_date`) VALUES($ref_no, $reg_no , 'Returned', '$buy_date', '$due_date')";
    if(signUp($sql)) {
        $sql = "DELETE FROM orders WHERE ref_no = $ref_no";
        if(signUp($sql )) {
            echo 'Success';
        }
        else {
            echo 'Failure';
        }
        }
        else {
            echo 'Failure';
        }
}
?>