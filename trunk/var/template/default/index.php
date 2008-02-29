<html>
<head>
<title>test</title>
</head>
<body>
<?php widget('Test')->to($archives); ?>
<div>
<select>
	<?php $archives->parse('<option value="{post_id}">{post_title}</option>');  ?>
</select>
</div>
</body>
</html>
