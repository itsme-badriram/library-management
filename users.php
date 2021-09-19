
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDA Library</title>
    <script src="jQuery.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
      var table = $('#datatable').dataTable();
      getUsers();
        function getUsers() {
          table.fnDestroy();
          $.post('restapi.php?getUsers=true', function(response) {
            response = JSON.parse(response);
            var content = '';
            for (const res of response) {
                content += '<tr><td>' + res['reg_no'] + '</td><td>' + res['name'] + '</td><td>' + res['contact'] + '</td></tr>';
            }
            document.getElementById('users-content').innerHTML = content;
            console.log(response[0]);
            table = $('#datatable').dataTable();

        });
        }
        $('#datatable tbody').on( 'click', 'tr', function () {
               var tableRow = $(this);
                $(this).toggleClass('selected');
                setTimeout(function() {
                    tableRow.toggleClass('selected');
                    console.log('Ues');
                }, 750);
               tableData = $(this).children('td').map(function() {
                    return $(this).text();
               }).get();
               console.log(tableData[0]);
               console.log(tableData[1]);
               console.log(tableData[2]);
               var str = '<label for="edit-reg-no">Registration Number</label> <input type="text" class="form-control" id="edit-reg-no" value="'+ tableData[0] +'" disabled><br><label for="edit-user-name">Name</label><input type="text" class="form-control" id="edit-user-name" value="'+ tableData[1] +'"><br><label for="edit-contact">Contact</label> <input type="text" class="form-control" id="edit-contact" value="'+ tableData[2] +'"><br>';
               $("#edit-modal-body").html(str); 
               $('#editUserModal').modal('show');
        });
        $('#edit-user-btn').on('click', function() {
              var regno = document.getElementById('edit-reg-no').value;
              var name= document.getElementById('edit-user-name').value;
              var contact = document.getElementById('edit-contact').value;
              $.post('restapi.php?editUser=true', {
                reg_no : regno,
                name: name,
                contact: contact
              }, function(response) {
                console.log(response);
                if(response === 'Success') {
                  alertBox('Record Edited Successfully');
                }
                else {
                  alertBox('Update Failed!');
                }
                getUsers();
                $('#editUserModal').modal('hide');
              });
            });
            $('#delete-user-btn').on('click', function() {
              $('#editUserModal').modal('hide');
              
              console.log(table);
             confirmBox('Do You Want to Delete this Record?', function() {
               
              //table.fnDestroy();
              var regno = document.getElementById('edit-reg-no').value;
              $.post('restapi.php?deleteUser=true', {
                reg_no : regno
              }, function(response) {
                console.log(response);
                if ( response === 'Success') {
                  getUsers();
                  alertBox('Record Deleted Successfully');
                }
                else {
                  alertBox('Record Deletion Failed');
                }
                
                
                
              });
               });
              
            });
        $('#add-user-btn').on('click', function() {
                var reg_no = document.getElementById('user-reg-no').value;
                var name = document.getElementById('user-name').value;
                var contact = document.getElementById('user-contact').value;
                $.post('restapi.php?addUser=true', {
                    reg_no : reg_no,
                    name: name,
                    contact: contact
                }, function(response) {
                    console.log(response);
                    $('#addUserModal').modal('hide');
                    if (response === 'Success') {
                      alertBox('User Added Successfully');
                    }
                    else {
                      alertBox('User Creation Failed');
                    }
                    getUsers();
                    $('#add-modal').trigger('reset');
                    
                });
               });

    });
    
    </script>
</head>
<body>
<?php require_once('confirmBox.php');  ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand h1" href="#">
<img src="img/images.jpg" width="35" height="35" alt="">   
<p class="d-inline align-middle">PDA Library Management</p> 
</a>
<div class="navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php"> Books </a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="users.php"> Users </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="checkout.php"> Checkout </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="transactions.php"> Renew/Return </a>
      </li>
    </ul>
  </div>
</nav>

<br><br>
<!--Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="add-modal-body">
      <form id="add-modal">
      <label for="user-reg-no">Registration Number</label> <input type="text" class="form-control" id="user-reg-no" ><br>
      <label for="user-name">Name</label><input type="text" class="form-control" id="user-name" ><br>
      <label for="user-contact">Contact</label> <input type="text" class="form-control" id="user-contact"><br>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="add-user-btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!--Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="edit-modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="edit-user-btn" class="btn btn-primary">Save changes</button>
        <button type="button" id="delete-user-btn" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<div class="card">

        <div class="card-body">
        <button type="button" id="add-user-btn" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal" >Add User</button>
          <div class="mt-4">
              <table class="display table nowrap responsive" id="datatable">
              <thead class="thead-dark">
                <tr>
                    
                <th>Reg No</th>
                    
                    <th>Name</th>
                    
                    <th>Contact</th>
                    
                </tr>
            </thead>
            <tbody id="users-content">

            </tbody>
                
                </table>
            </div>
        </div>
    </div>
</body>
</html>