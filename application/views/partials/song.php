<tr>
	<td><strong><?php echo $count; ?></strong></td>
	<td><?php echo $artist; ?></td>
	<td><?php echo $title; ?></td>
	<td><?php echo $album; ?></td>
	<td><?php if($url!="") echo anchor($url,$url,array("target"=>"_blank")); ?></td>
</tr>