<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDA Library</title>
    <script src="jQuery.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script type="text/javascript">
    var tableData;
        $(document).ready(function() {

          var table = $('#datatable').dataTable();

          getBooks();
          function getBooks() {
            table.fnDestroy();
            $.post('restapi.php?getBooks=true', function(response) {
                response = JSON. parse(response);
                var content = '';
                for (const res of response) {
                    content += '<tr><td>' + res['ref_no'] + '</td><td>' + res['title'] + '</td><td>' + res['authors'] + '</td><td>' + res['type'] + '</td></tr>';
                }
                document.getElementById('books-content').innerHTML = content;
                table = $('#datatable').dataTable();
            });
          }

            $('#add-book-btn').on('click', function() {
              var refno = document.getElementById('ref-no').value;
              var title = document.getElementById('book-title').value;
              var authors = document.getElementById('book-author').value;
              var type = document.getElementById('book-type').value;
              $.post('restapi.php?addBook=true', {
                ref_no : refno,
                title: title,
                authors: authors,
                type: type
              }, function(response) {
                console.log(response);
                if(response === 'Success') {
                  alertBox('Record Added Successfully');
                }
                else {
                  alertBox('Update Failed!');
                }
                getBooks();
                $('#addBookModal').modal('hide');
                $('#add-modal').trigger('reset');
              });
            });
            $('#delete-book-btn').on('click', function() {
              $('#editBookModal').modal('hide');
              
              console.log(table);
             confirmBox('Do You Want to Delete this Record?', function() {
               
              //table.fnDestroy();
              var refno = document.getElementById('edit-ref-no').value;
              $.post('restapi.php?deleteBook=true', {
                ref_no : refno
              }, function(response) {
                console.log(response);
                if ( response === 'Success') {
                  getBooks();
                  alertBox('Record Deleted Successfully');
                }
                else {
                  alertBox('Record Deletion Failed');
                }
                
                
                
              });
               });
              
            });
            $('#edit-book-btn').on('click', function() {
              var refno = document.getElementById('edit-ref-no').value;
              var title = document.getElementById('edit-book-title').value;
              var authors = document.getElementById('edit-book-author').value;
              var type = document.getElementById('edit-book-type').value;
              console.log(refno, title, authors, type);
              $.post('restapi.php?editBook=true', {
                ref_no : refno,
                title: title,
                authors: authors,
                type: type
              }, function(response) {
                console.log(response);
                if(response === 'Success') {
                  alertBox('Record Edited Successfully');
                }
                else {
                  alertBox('Update Failed!');
                }
                getBooks();
                $('#editBookModal').modal('hide');
              });
            });


           $('#datatable tbody').on( 'click', 'tr', function () {
               tableData = $(this).children('td').map(function() {
                    return $(this).text();
               }).get();
               console.log(tableData[0]);
               console.log(tableData[1]);
               console.log(tableData[2]);
               console.log(tableData[3]);

               var str='<label for="edit-ref-no">Reference Number</label> <input type="text" class="form-control" id="edit-ref-no" value="'+ tableData[0] +'" disabled><br><label for="edit-book-title">Book Title</label><input type="text" class="form-control" id="edit-book-title" value="'+ tableData[1] +'"><br><label for="edit-book-author">Author</label> <input type="text" class="form-control" id="edit-book-author" value="'+ tableData[2] +'"><br><label for="edit-book-type">Language</label> <input type="text" class="form-control" id="edit-book-type" value="'+ tableData[3] +'">';
               $("#edit-modal-body").html(str); 
               $('#editBookModal').modal('show');
           });
        });
    </script>
</head>
<body>
<?php require_once('confirmBox.php'); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand h1" href="#">
<img src="img/images.jpg" width="35" height="35" alt="">   
<p class="d-inline align-middle">PDA Library Management</p> 
</a>
<div class="navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="index.php"> Books </a>
      </li>
      <li class="nav-item">
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

<!--Edit Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" role="dialog" aria-labelledby="editBookModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="edit-modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="edit-book-btn" class="btn btn-primary">Save changes</button>
        <button type="button" id="delete-book-btn" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!--Add Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" role="dialog" aria-labelledby="addBookModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBookModalLabel">Add Book</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="add-modal-body">
      <form id="add-modal">
      <label for="ref-no">Reference Number</label> <input type="text" class="form-control" id="ref-no" ><br>
      <label for="book-title">Book Title</label><input type="text" class="form-control" id="book-title" ><br>
      <label for="book-author">Authors</label> <input type="text" class="form-control" id="book-author"><br>
      <label for="book-author">Language</label> <input type="text" class="form-control" id="book-type">
      </div>
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="add-book-btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<br><br>
<div class="card">

        <div class="card-body">
        <button type="button" id="addbook-btn" class="btn btn-primary" data-toggle="modal" data-target="#addBookModal" >Add Book</button>
          <div class="mt-4">
              <table class="display table nowrap responsive" id="datatable">
              <thead class="thead-dark">
                <tr>
                    
                <th>Ref No</th>
                    
                    <th>Title</th>
                    
                    <th>Authors</th>
                    
                    <th>Type</th>
                    
                    
                </tr>
            </thead>
            <tbody id="books-content">

            </tbody>
                
                </table>
            </div>
        </div>
    </div>
</body>
</html>