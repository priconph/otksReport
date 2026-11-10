<?php
	require_once('includes.php');
?>

<form id="frm-upload" action="#" method="post" enctype="multipart/form-data">
	<input type="file" name="file_1" id="file_1" required>
	<input type="submit" value="1">
	<input type="submit" value="2">
	
</form>


<div id="test"> 
</div>

<script>
$('#frm-upload').on('submit',(function(e) {
  e.preventDefault();
  var form_data = new FormData(this);
  form_data.append("action","upload_attendance_csv");
  $.ajax({
	   url: "./test/upload.php",    // Url to which the request is send
	   type: "POST",             // Type of request to be send, called as method
	   // dataType: "json",
	   data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
	   contentType: false,       // The content type used when sending data to the server.
	   cache: false,             // To unable request pages to be cached
	   processData:false,        // To send DOMDocument or non processed data file it is set to false
	   success: function(data)   // A function to be called if request succeeds
	   {
		$('#test').empty();
		$('#test').append(data);
		$('#tbl_attendance_update').DataTable();
	   }, error : function (data) {

	   }
  }); 
 }));

</script>


