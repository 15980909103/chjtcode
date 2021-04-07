<ul class="pagination">
	<li ><a href="<?php echo $url,$firstIndex; ?>">&laquo;</a></li>
	<?php if($preIndex!=-1){ ?>
	<li><a href="<?php echo $url,$preIndex; ?>">&lt;</a></li>
	<?php } ?>
	<?php for($i=$startIndex;$i<=$targetIndex;$i++){
		 if($i==$currentIndex){
			 echo  '<li class="active"><a href="">',$i,'</a></li>';
		 }else{
			 echo "<li><a href='$url$i'>$i</a></li>";
		 }
	}
	?>
	<?php if($nextIndex!=-1){ ?>
		  <li><a href="<?php echo $url,$nextIndex; ?>">&gt;</a></li>
	<?php } ?>
	<li><a href="<?php echo $url,$lastIndex; ?>">&raquo;</a></li>
</ul>