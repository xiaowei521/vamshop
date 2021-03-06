<div id="comment">
	<h4 class="title">
		<i class="glyphicon glyphicon-comment"></i> 评论 (<?php echo mc_comment_count($page_id); ?>)
	</h4>
	<?php if(mc_user_id()) : ?>
	<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/editor/summernote.css">
					<script src="<?php echo mc_site_url(); ?>/editor/summernote.min.js"></script>
					<script src="<?php echo mc_site_url(); ?>/editor/summernote-zh-CN.js"></script>
	<form role="form" class="mb-20" method="post" action="<?php echo U('home/perform/comment'); ?>" onsubmit="return postForm()">
		<div class="form-group">
			<textarea name="content" class="form-control" rows="3" id="summernote" placeholder="请输入评论内容"></textarea>
		</div>
		<div class="text-center">
			<button type="submit" class="btn btn-block btn-warning">
				<i class="glyphicon glyphicon-ok"></i> 提交
			</button>
		</div>
		<input type="hidden" name="id" value="<?php echo $page_id; ?>">
	</form>
	<script type="text/javascript">
					$(document).ready(function() {
						$('#summernote').summernote({
							height: "200px",
							lang:"zh-CN",
							toolbar: [
							    ['insert', ['picture']]
							],
							onImageUpload: function(files) {
							    var file = files[0]; 	
						        //判断类型是不是图片
						        if(!/image\/\w+/.test(file.type)){   
						                alert("请确保文件为图像类型"); 
						                return false; 
						        }
						        var reader = new FileReader(); 
						        reader.readAsDataURL(file); 
						        reader.onload = function(e){ 
						        	//alert(this.result);
						        	$.ajax({
										type: 'POST',
										url: '<?php echo mc_site_url(); ?>/index.php?m=home&c=perform&a=publish_img',
										data:{src:this.result},
										success: function(data) {
											$("#summernote").summernote("insertImage", data, file.name);
										}
									});
						        } 
							}
						});
					});
					var postForm = function() {
						var content = $('textarea[name="content"]').html($('#summernote').code());
					}
					</script>
	<?php else : ?>
	<form role="form" class="mb-20">
		<div class="form-group">
			<textarea id="comment-textarea" name="content" class="form-control" rows="3" placeholder="请输入评论内容" disabled></textarea>
			<p class="help-block">您必须在<a href="#" data-toggle="modal" data-target="#loginModal">登陆</a>或<a href="#" data-toggle="modal" data-target="#registerModal">注册</a>后，才可以发表评论！</p>
		</div>
	</form>
	<?php endif; ?>
	<?php if(mc_comment_count($page_id)) : ?>
	<?php foreach($comment as $val) : ?>
	<div class="media" id="comment-<?php echo $val['id']; ?>">
		<div class="media-left">
			<a class="img-div img-circle" href="javascript:;">
				<img class="media-object" src="<?php echo mc_user_avatar($val['user_id']); ?>" alt="<?php echo mc_user_display_name($val['user_id']); ?>">
			</a>
		</div>
		<div class="media-body">
			<h4 class="media-heading">
				<a href="javascript:;"><?php echo mc_user_display_name($val['user_id']); ?></a>
				<?php if(mc_get_meta($val['user_id'],'user_level',true,'user')==10) : ?><span class="btn btn-danger btn-xs">管理员</span><?php elseif(mc_get_meta($val['user_id'],'user_level',true,'user')==6) : ?><span class="btn btn-info btn-xs">网站编辑</span><?php endif; ?>
				<small class="pull-right hidden-xs"><?php echo date('Y-m-d H:i:s',$val['date']); ?></small>
				<?php if(mc_get_meta(mc_user_id(),'user_level',true,'user')>5) : ?>
				<form class="inline hidden-xs" method="post" action="<?php echo U('home/perform/comment_delete'); ?>">
					<button type="submit" class="btn btn-danger btn-xs pull-right">删除</button>
					<input type="hidden" name="id" value="<?php echo $val['id']; ?>">
				</form>
				<?php endif; ?>
			</h4>
			<p><?php echo mc_magic_out($val['action_value']); ?></p>
			<a class="btn btn-default btn-xs btn-huifu" href="#comment-textarea" huifu-data="<?php echo $val['id']; ?> ">回复</a>
			<?php 
				$comment2 = M('action')->where("page_id='".$val['id']."' AND action_key='comment2'")->select();
				if($comment2) :
			?>
			<ul class="mt-20 list-unstyled">
				<?php foreach($comment2 as $val2) : ?>
				<li id="comment-<?php echo $val2['id']; ?>"><a href="<?php echo U('user/index/index?id='.$val2['user_id']); ?>"><?php echo mc_user_display_name($val2['user_id']); ?></a>：<?php echo $val2['action_value']; ?></li>
				<?php endforeach; ?>
			</ul>	
			<?php endif; ?>
		</div>
	</div>
	<?php endforeach; ?>
	<?php if($_GET['comment']!='all' && mc_comment_count($page_id)>10) : ?>
	<a href="<?php echo mc_get_url($page_id); ?><?php if(C('URL_MODEL')==2) : ?>?comment=all<?php else : ?>&comment=all<?php endif; ?>#comment" rel="nofollow" class="mt-20 btn btn-default btn-block">更多评论</a>
	<?php endif; ?>
<script>
	$('.btn-huifu').click(function(){
		var huifu = $(this).attr('huifu-data');
		$(this).after('<form class="mt-20" role="form" method="post" action="<?php echo U('home/perform/comment'); ?>"><div class="form-group"><textarea id="huifu'+huifu+'" name="content" class="form-control" rows="3" placeholder="请输入回复内容"></textarea></div><button type="submit" class="btn btn-default btn-block btn-sm"><i class="glyphicon glyphicon-ok"></i> 提交</button></div><input type="hidden" name="id" value="<?php echo $page_id; ?>"><input type="hidden" name="parent" value="'+huifu+'"></form>');
		$('#huifu'+huifu).focus();
		$(this).remove();
		return false;
	});
</script>
<?php endif; ?>
</div>