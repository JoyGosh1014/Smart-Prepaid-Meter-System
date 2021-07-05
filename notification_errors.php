<?php  
if (count($notification_errors) > 0): ?>
	
		<?php foreach ($notification_errors as $notification_error) : ?>
			<div class="alert alert-danger" role="alert">
				<?php echo $notification_error; ?>
			</div>
		<?php endforeach; ?>
	
<?php  endif ?>