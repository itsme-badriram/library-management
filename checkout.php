
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
    var tableData;
    var checkoutData = [];
    var query = [];
        $(document).ready(function() {
            $('#reg-no').keyup(function() {
                var searchText = $(this).val();
                if(searchText != '') {
                    $.ajax( {
                        url: 'restapi.php?search=true',
                        method: 'post',
                        data: { query: searchText },
                        success: function(response) {
                            $('#show-list').html(response);
                        }
                    });
                }
                else {
                    $('#show-list').html('');
                }
            });
            $(document).on('click', '.list-group a', function() {
                $('#reg-no').val($(this).text());
                $.post('restapi.php?getUser=true', {
                    reg_no: $(this).text()
                }, function(response) {
                    response = JSON.parse(response);
                    console.log(response);
                    var str='<div class="col-sm"><label for="name">Name</label> <input type="text" class="form-control" id="name" value="'+ response['name'] +'"  disabled></div> <div class="col-sm"><label for="contact">Contact</label> <input type="text" class="form-control" value="'+ response['contact'] +'" id="contact" disabled> </div>';
                    document.getElementById('user-details').innerHTML = str;
                    $('#checkout-btn').css('display', 'block');
                });
                $('#show-list').html('');
            });

            $('#checkout-btn').on('click', function() {
                query = [];
                $.each($('.checkout-item'), function() {
                    var ref_no = $(this).children('.item-ref-no').text();
                    var title = $(this).children('.item-title').text();
                    var author = $(this).children('.item-author').text();
                   var days = $(this).children('.item-days').children('.form-group').children('#sel1').children(' option:selected').val();
                    var element = {
                        reg_no : document.getElementById('reg-no').value,
                        ref_no : ref_no,
                        days: days
                    };
                    query.push(element);
                    
                });
                if (query.length > 0) {
                  
                  confirmBox('Do You Want To Checkout The Selected Items?', function() {
                    
                    $.ajax({
                    url: 'restapi.php?checkout=true',
                    type: 'post',
                    data : JSON.stringify(query),
                    dataType: 'html',
                    success: function (data) {
                        console.log(data);
                        if (data === 'Success') {
                          alertBox('Checkout Successful');
                          $('#checkout-content').html('<tr class="no-items"> <td> No Items for Checkout!</td> </tr>');
                          $('#reg-no').val('');
                          document.getElementById('user-details').innerHTML = '';
                        }
                        else {
                          alertBox('Checkout Failed');
                        }
                    }
                });
                   });
                }
                else {
                  alertBox('No Items Added For Checkout');
                }
                
                
            });
            $.post('restapi.php?getBooks=true', function(response) {
                response = JSON. parse(response);
                var content = '';
                for (const res of response) {
                    content += '<tr><td>' + res['ref_no'] + '</td><td>' + res['title'] + '</td><td>' + res['authors'] + '</td><td>' + res['type'] + '</td><td>' + res['status'] + '</td></tr>';
                }
                document.getElementById('books-content').innerHTML = content;
                console.log(response[0]);
                var table = $('#datatable').dataTable();
            });
            $('#checkout-btn').css('display', 'none');
           
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
               console.log(tableData[3]);
               var data;
               if (checkoutData && checkoutData.length > 0) {
                data = checkoutData.filter(d => d.ref_no == tableData[0]);
               }
               if (data && data[0]) {
                alertBox('Book Already Added to Checkout!');
               }
               else {
                if (tableData[4] === 'Available') {
                  var element = {
                    ref_no : tableData[0],
                    title: tableData[1],
                    author: tableData[2]
                };
                checkoutData.push(element);
                $('.no-items').remove();
                $('#checkout-content').append('<tr class="checkout-item"><td class="item-ref-no">'+ element.ref_no +'</td><td class="item-title">'+ element.title +'</td><td class="item-author">'+ element.author +'</td><td class="item-days"><div class="form-group"><select class="form-control" id="sel1"><option value="15">15 Days</option> <option value="30">30 Days</option></select></div></td><td><span class="checkout-remove" id="' + element.ref_no + '">&#10006;</span></td></tr>');

                $(document).on('click', '.checkout-remove', function() {
                $(this).parent().parent().remove();
                var id = $(this).attr('id');
                console.log(id);
                checkoutData = checkoutData.filter(d => d.ref_no != id);
                if( checkoutData.length == 0) {
                    var str = '<tr class="no-items"> <td> No Items for Checkout!</td> </tr>';
                    $('#checkout-btn').css('display', 'none');
                    $('#checkout-content').html(str);
                }
                });
                }
                else {
                  alertBox('Selected Book is Not Available!');
                }

               }

                
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
                    alertBox('User Created Successfully');
                    $('#checkout-content').html('<tr class="no-items"> <td> No Items for Checkout!</td> </tr>');
                    $('#reg-no').val('');
                    document.getElementById('user-details').innerHTML = '';
                    
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
      <li class="nav-item ">
        <a class="nav-link" href="index.php"> Books </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="users.php"> Users </a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="checkout.php"> Checkout </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="transactions.php"> Renew/Return </a>
      </li>
    </ul>
  </div>
</nav>
<br>
<div class="container">
  <div class="row">
    <div class="col-sm-8">
    <label for="reg-no">Registration Number</label> <input type="text" class="form-control" id="reg-no" ><br>
    </div>
    <div class="col-sm-1 mt-4" style="position: relative;">
    <button type="button" style="position: relative; top: 8px;" onClick="window.location.reload()" class="btn btn-primary">Refresh</button>
    </div>
    <div class="col-sm-2 mt-4" style="position: relative;">
    <button type="button" style="position: relative; top: 8px;"  class="btn btn-secondary" data-toggle="modal" data-target="#addUserModal">Create User</button>
    </div>
    <div class="col-sm-8" style="position: relative; margin-top: -23px;"> 
    <div class="list-group" id="show-list">
        
    </div>
    </div>
  </div><br>
  <div class="row" id="user-details">
        
  </div>
</div>

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
      <label for="user-reg-no">Registration Number</label> <input type="text" class="form-control" id="user-reg-no" ><br>
      <label for="user-name">Name</label><input type="text" class="form-control" id="user-name" ><br>
      <label for="user-contact">Contact</label> <input type="text" class="form-control" id="user-contact"><br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="add-user-btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<br>
<div class="card">
        <div class="card-body">
            <div class="mt-4">
                <table class="display table nowrap responsive" id="checkoutTable">
                <thead class="thead-light">
                <tr>
                    
                    <th>Ref No</th>
                    
                    <th>Title</th>
                    
                    <th>Authors</th>
                    <th>Days</th>
                    <th></th>
                    
                </tr>
            </thead>
            <tbody id="checkout-content"> 
            <tr class="no-items"> <td> No Items for Checkout!</td> </tr>

            </tbody>
                </table>
            </div>
            <button type="button" id="checkout-btn" class="btn btn-success" >Checkout</button>
        </div>

</div>
<br><br>
<div class="card">
    
        <div class="card-body">
          <div class="mt-4">
              <table class="display table nowrap responsive" id="datatable">
              <thead class="thead-dark">
                <tr>
                    
                <th>Ref No</th>
                    
                    <th>Title</th>
                    
                    <th>Authors</th>
                    
                    <th>Type</th>
                    
                    <th>Status</th>
                    
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