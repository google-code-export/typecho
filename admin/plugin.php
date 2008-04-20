<?php include( 'header.php' ); ?>

	<div id="main">
		<h2>Plugin Settings</h2>
		<div id="page">

			<table class="latest" id="plugin">
				<tr>
					<th width="15%">plugin</th>
					<th width="5%">version</th>
					<th width="57%">description</th>
					<th width="7%">author</th>
					<th width="6%">status</th>
					<th width="10%">actions</th>
				</tr>
				<?php for($a=0;$a!=3;$a++) echo'
				<tr>
					<td><a href="#">Plugin Name</a></td>
					<td>1.0.0</td>
					<td><p>MultiBlog allows you to publish content from other blogs and define publishing rules and access controls between them.</p></td>
					<td><a href="#">Admin</a></td>
					<td>Inctive</td>
					<td style="color: #C5D8EB;"><a href="#">Active</a> | <a href="#">Edit</a></td>
				</tr>'; ?>
				<tr>
					<td colspan="2" style="background: #fff; border: none;"></td>
					<td colspan="4" style="background: #fff; border: none;"><hr class="space" /><a href="#" class="right" style="margin-right: .5em;">&times;</a><h4>Editing (Plugin Name)</h4><textarea id="" rows="20" cols="" style="width: 98%; margin: 0 1%;" class="code"></textarea><input type="submit" value="Save Changes" style="margin: 1em 1em 1em .6em;" /><input type="submit" value="Cancel" style="margin: 1em 0 1em;" /></td>
				</tr>
				<?php for($a=0;$a!=3;$a++) echo'
				<tr>
					<td><a href="#">Plugin Name</a></td>
					<td>1.0.0</td>
					<td>MultiBlog allows you to publish content from other blogs and define publishing rules and access controls between them.</td>
					<td><a href="#">Admin</a></td>
					<td>Inctive</td>
					<td style="color: #C5D8EB;"><a href="#">Active</a> | <a href="#">Edit</a></td>
				</tr>'; ?>
			</table>
			<hr class="space" />
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php include( 'footer.php' ); ?>
