<?php 

	// Connect to database. You need to know the host name, user name, and password
	$connection = mysqli_connect( "localhost", "root", "root", "mhudson" );
	// This variable $connection will be used with every mysqli call. 
	
	// Display an error if there was a problem. 
	if (mysqli_errno()) {
		echo "Could not connect to databse.";
	}
	

	// Check if tags were submitted from the form below. 
	// This block of code is only run when the page is submitted from the form below. 
	if (isset($_POST['tags'])) {
		$tags = $_POST['tags'];
		
		// Convert to lowercase
		$tags = strtolower($_POST['tags']);
		
		// Explode tags on comma. Converts string to an array on the the ,
		$tags_array = explode(",", $tags);
		
		// Loop through all tags. 
		// I'm also keeping track of the number of times a tag appears. 
		// If a tag does not exist we'll add it, and give it a count of 1. 
		// If a tag does exist we'll increment it's count by 1
		
		foreach ( $tags_array as $tag ) {
			// Remove special characters 
			$tag = preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $tag);
			
			// check for an existing tag
			$results_for_tag = mysqli_query( $connection, "SELECT * FROM doodle_tags WHERE tag='$tag' LIMIT 1" );
			
			// There are 0 records returned this tag does not exist so add it to the database. 
			if (mysqli_num_rows( $results_for_tag ) == 0) {
				// Check that tag is not an empty string. Could check for this before submitting form. 
				if ($tag != "") {
					// There are no tags with this name, add one to the table
					$results_insert_tag = mysqli_query($connection, "INSERT INTO doodle_tags (tag, count) VALUES('$tag', '1')");
				}
			} else {
				// The tag already exists, so we'll update the count for that tag. 
				// Get the record returned. 
				$row = mysqli_fetch_array($results_for_tag);
				
				// Get the id of that record. 
				$tag_id = $row['id'];
				
				// Update the count
				$results_tag_update_count = mysqli_query( 
					$connection, 
					"UPDATE doodle_tags 
					SET count = count + 1 
					WHERE id=$tag_id" 
				);
			}
		}
	}
?>



<html>
	<head>
		<title>Tags System test</title>
	</head>
	<body>
		<div>
			<h1>Add Tag</h1>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<small>Separate tags with a comma.</small>
				<p>
					<input id="tags" name="tags" type="text">
					<input id="tags-submit" name="submit" type="submit">
				</p>
			</form>
		</div>
		
		<div>
			<h1>List Tags</h1>
			<ul>
				<?php
					// Get all of the tags from the table, order them on the tag field. 
					$result = mysqli_query($connection, "SELECT * FROM doodle_tags ORDER BY tag");
					
					// Print an error if there is a problem. 
					echo mysqli_errno();
					
					// Loop through the results and print some <li>
					// Print the tag name and the count. 
					while( $row = mysqli_fetch_array($result) ) {
						$tag = $row["tag"];
						$tag_count = $row["count"];
						echo "<li>$tag ($tag_count)</li>";
					}	
				?>
			</ul>
		</div>
	</body>
</html>