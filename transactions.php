
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
      var table = $('#data_table').dataTable();
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
            function getBooks(reg_no) {
              $.post('restapi.php?getUserBooks=true', {reg_no : reg_no }, function(response) {
            response = JSON.parse(response);
            var history_content = '';
            var curr_content = '';
                for (const res of response) {
                  var str='';
                  if (res['status'] === 'Ongoing') {
                    str = '<td class="renew"><span class="renew-icon" >&#8635;</span></td><td class="return"><span  class="return-icon">&#10150;</span></td>';
                    curr_content += '<tr><td class="book-ref-no">' + res['ref_no'] + '</td><td>' + res['title'] + '</td><td>' + res['authors'] + '</td><td>' + res['buy_date'] + '</td><td>' + res['due_date'] + '</td><td>'+ res['days'] +'</td>'+ str +'</tr>';
                  }
                  else {
                    history_content += '<tr><td class="book-ref-no">' + res['ref_no'] + '</td><td>' + res['title'] + '</td><td>' + res['authors'] + '</td><td>' + res['buy_date'] + '</td><td>' + res['due_date'] + '</td><td>'+ res['days'] +'</td><td>'+ res['status'] +'</td></tr>';
                  }
                    
                }
                document.getElementById('books-content').innerHTML = history_content;
                if (curr_content) {

                }
                else {
                  curr_content = '<tr class="no-items"> <td> No Items for Return/Renewal!</td> </tr>';
                }
                document.getElementById('table-content').innerHTML = curr_content;
                $('.renew-icon').on('click', function() {
                  var ref_no = $(this).parent().parent().children('.book-ref-no').text();
                    confirmBox('Do You Want To Renew The Selected Book?', function() {
                      
                    $.post('restapi.php?renew=true', {ref_no: ref_no, reg_no : reg_no}, function(response){
                      
                      console.log(response);
                      if (response === 'Success') {
                        alertBox('Book Renewed Successfully');  
                      }
                      else {
                        alertBox('Book Renewal Failed'); 
                      }
                      table.fnDestroy();
                      getBooks(reg_no);
                    });
                    });

                });
                $('.return-icon').on('click', function() {
                  var ref_no = $(this).parent().parent().children('.book-ref-no').text();
                  confirmBox('Do You Want To Return The Selected Book?', function() {
                    
                  console.log(ref_no);
                    $.post('restapi.php?return=true', {ref_no: ref_no, reg_no : reg_no}, function(response){
                      
                      console.log(response);
                      if(response === 'Success') {
                        alertBox('Book Returned Successfully');
                      }
                      else {
                        alertBox('Book Return Failed');
                      }
                      table.fnDestroy();
                      getBooks(reg_no);
                    });

                    });


                  

                });
                table = $('#data_table').dataTable({"order": [[ 6, "asc" ]]});
        });
            }
        $(document).on('click', '.list-group a', function() {
            $('#reg-no').val($(this).text());
            var reg_no = $(this).text();
            table.fnDestroy();
            $.post('restapi.php?getUser=true', {
                reg_no: $(this).text()
            }, function(response) {
                response = JSON.parse(response);
                console.log(response);
                var str='<div class="col-sm"><label for="name">Name</label> <input type="text" class="form-control" id="name" value="'+ response['name'] +'"  disabled></div> <div class="col-sm"><label for="contact">Contact</label> <input type="text" class="form-control" value="'+ response['contact'] +'" id="contact" disabled> </div>';
                document.getElementById('user-details').innerHTML = str;
                getBooks(reg_no);
                
            $('#show-list').html('');
        });
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
      <li class="nav-item">
        <a class="nav-link" href="index.php"> Books </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="users.php"> Users </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="checkout.php"> Checkout </a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="transactions.php"> Renew/Return </a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
  <div class="row">
    <div class="col-sm-8">
    <label for="reg-no">Registration Number</label> <input type="text" class="form-control" id="reg-no" ><br>
    </div>
    <div class="col-sm-1 mt-4">
    <button type="button" style="position: relative; top: 8px;" onClick="window.location.reload()" class="btn btn-primary">Refresh</button>
    </div>
    <div class="col-sm-8" style="position: relative; margin-top: -23px;"> 
    <div class="list-group" id="show-list">
        
    </div>
    </div>
  </div><br>
  <div class="row" id="user-details">
        
  </div>
</div>
<br>
<div class="card">
        <div class="card-body">
            <div class="mt-4">
                <table class="display table nowrap responsive" id="curr_data_table">
                <thead class="thead-light">
                <tr>
                <th>Ref No</th>
                    
                    <th>Title</th>
                    
                    <th>Authors</th>
                    <th> Date  </th>
                    <th> Due Date  </th>
                    <th>Days</th>
                    <th style="text-align: center"> Renewal</th>
                    <th style="text-align: center">  Return </th>
                    
                </tr>
            </thead>
            <tbody id="table-content"> 
            <tr class="no-items"> <td> No Items for Return/Renewal!</td> </tr>

            </tbody>
                </table>
            </div>
        </div>

</div>

<div class="card">

        <div class="card-body">
          <div class="mt-4">
              <table class="display table nowrap responsive" id="data_table">
              <thead class="thead-dark">
                <tr>
                    
                <th>Ref No</th>
                    
                    <th>Title</th>
                    
                    <th>Authors</th>
                    <th> Date  </th>
                    <th> Due Date  </th>
                    <th>Days</th>
                    <th> Status </th>
                </tr>
            </thead>
            <tbody id="books-content">
            </tbody>
                
                </table>
            </div>
        </div>
    </div>
    <br><br>
</body>
</html>