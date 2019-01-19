<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_POST || $_FILES) {
	
	/**
	 * Configuration
	 */

	# IE9 and earlier don't support application/json response, which force to download instead.
	$config['jsonResponseFormat'] = false;

	// print_r($_POST);
	// print_r($_FILES);exit;

	# JSON Response Format
	if ($config['jsonResponseFormat']) 
		header('Content-Type: application/json; charset=utf-8');
	
	$file = isset($_FILES['single_img']) ? $_FILES['single_img'] : NULL;
	$files = isset($_FILES['multiple_img']) ? $_FILES['multiple_img'] : NULL;

	$callback = ['code'=>404, 'msg'=>'No input'];

	if ($file && $file['name']) {
		
		$extensionMap = ['jpg', 'jpeg', 'gif', 'png'];
		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		# Check supported extension
		if (in_array($extension, $extensionMap)) {

			if (!$file['tmp_name']) {
				$callback = ['code'=>400, 'msg'=>'Your file may exceed the max upload size'];
			}

			$filePath = 'uploaded_images/image.'.$extension;
			$result = move_uploaded_file($file['tmp_name'], $filePath);

			if ($result) {

				$callback = ['code'=>200, 'image'=>$filePath];
			}

		} else {

			$callback = ['code'=>403, 'msg'=>'Unsupported file extension'];
		}
	} 
	elseif ($files && $files['name']) {
		
		$extensionMap = ['jpg', 'jpeg', 'gif', 'png'];

		$flag[403] = 0;
		$imagePathList = [];

		foreach ($files['name'] as $key => $fileName) {
	
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

			# Check supported extension
			if (in_array($extension, $extensionMap)) {
				
				if (!$files['tmp_name'][$key]) {
					
					$errorCallback = ['code'=>400, 'msg'=>'Your file may exceed the max upload size'];
				}
				
				$filePath = "uploaded_images/image[{$key}].{$extension}";
				$result = move_uploaded_file($files['tmp_name'][$key], $filePath);

				$imagePathList[] = $result ? $filePath : NULL;

			} else {

				$flag[403] = 1;
			}
		}

		$code = $flag[403] ? 403 : 200;

		$callback = isset($errorCallback) ? $errorCallback : ['code'=>$code, 'images'=>$imagePathList];
	}
	
	echo json_encode($callback);
	exit();
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>AJAX File Upload using FormData & jQuery lib</title>
</head>
<body align="center">

<!--Container-->
<div align="left" style="width:500px;margin:0px auto;">

	<h3>AJAX File Upload using FormData & jQuery lib</h3>

	<h5>FormData File Upload</h5>

	<form id="data" method="POST">
	  Single Image Upload:<br/>
	  <input id="formdata-single" name="single_img" type="file" /><br />
	  Mutiple Image Upload:<br/>
	  <input id="formdata-multiple" name="multiple_img[]" type="file" multiple /><br />
	  <!-- <input type="submit" value="Submit"> -->
	</form>

	<h5>jQuery File Upload</h5>
	Single:<br/>
	<input id="jq-fileupload-single" type="file" name="single_img" /><br />
	Multi-Single:<br/>
	<input id="jq-fileupload-multi-single" type="file" name="single_img" multiple /><br />
	Multiple:<br/>
	<input id="jq-fileupload-multiple" type="file" name="multiple_img[]" multiple /><br />
	<br />
	<img id="showImg" src="" style="display:none;max-width:500px;max-height:500px;" />

</div>
<!--/Container-->

	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript">
		// Program a custom submit function for the form
		// $("form#data").submit(function(event) {	// Trigger by submit
		$("#formdata-single").change(function(event) {	// Triger after select 

		  imageLoading();

		  // Disable the default form submission
		  event.preventDefault();

		  $form = $("form#data");
		 
		  // Grab all form data  
		  var formData = new FormData($form[0]);
		 
		  $.ajax({
		    url: 'index.php',
		    type: 'POST',
		    data: formData,
		    dataType: 'json',
		    async: false,
		    cache: false,
		    contentType: false,
		    processData: false,
		    success: function (data) {

		    	callbackProcess(data);
		    }
		  });
		  return false;
		});

		$("#formdata-multiple").change(function(event) {	// Triger after select 

		  imageLoading();

		  // Disable the default form submission
		  event.preventDefault();

		  $form = $("form#data");
		 
		  // Grab all form data  
		  var formData = new FormData($form[0]);
		 
		  $.ajax({
		    url: 'index.php',
		    type: 'POST',
		    data: formData,
		    dataType: 'json',
		    async: false,
		    cache: false,
		    contentType: false,
		    processData: false,
		    success: function (data) {

		    	callbackProcess(data);
		    }
		  });
		  return false;
		});
	</script>

	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->
	<script src="jquery-fileupload/js/vendor/jquery.ui.widget.js"></script>
	<script src="jquery-fileupload/js/jquery.iframe-transport.js"></script>
	<script src="jquery-fileupload/js/jquery.fileupload.js"></script>
	<script>
	$(function () {

		$('#jq-fileupload-single').fileupload({
			url: 'index.php',
	        dataType: 'json',
	        add: function (e, data) {
	        	imageLoading();
	            data.submit();
	        },
	        done: function (e, data) {
	        	var result = data.result;
	        	callbackProcess(result);
	        }
	    });

	    $('#jq-fileupload-multi-single').fileupload({
	    	url: 'index.php',
	        dataType: 'json',
	        singleFileUploads: false,
	        add: function (e, data) {
	        	imageLoading();
	            data.submit();
	        },
	        done: function (e, data) {
	        	var result = data.result;
	        	callbackProcess(result);
	        }
	    });

	    $('#jq-fileupload-multiple').fileupload({
	    	url: 'index.php',
	        dataType: 'json',
	        singleFileUploads: false,
	        add: function (e, data) {
	        	imageLoading();
	            data.submit();
	        },
	        done: function (e, data) {
	        	var result = data.result;
	        	callbackProcess(result);
	        }
	    });
	});
	</script>


	<script type="text/javascript">
		
		function callbackProcess (data) {
			
			// console.log(data);
	    	if (data.code==200) {

	    		data.image = (typeof data.image != 'undefined') ? data.image : data.images[0];

	    		// Show Image
				d = new Date();
				$("#showImg").fadeOut(function(){
					$(this).attr('src', data.image+'?'+d.getTime()).fadeIn();
				});

	      	} else {

	      		alert(data.msg);
	      	};
	    };

	    function imageLoading () {
	    	
	    	$("#showImg").fadeOut(function(){
				$(this).attr('src', 'loading_icon.gif').fadeIn();
			});
	    }
		
	</script>
</body>
</html>