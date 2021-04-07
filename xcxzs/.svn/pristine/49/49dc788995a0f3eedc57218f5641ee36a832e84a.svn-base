<div class="panel">
    <div class="breadcrumb">
		<span class='icon icon-home'></span>
		<a href="#">微信菜单管理</a>
	</div>
    <div class="panel-body">
        <div class="row">
			<div class="rspan12">
				<a class="btn-x btn-red" href="/xiamenyyhoutai/wechat/addMenu">添加菜单</a>
				<a class="btn-x btn-red" href="/xiamenyyhoutai/wechat/postMenu">发布菜单</a>
			</div>
        </div>
<table class="table1">
	<thead>
		<tr>
			<th>菜单</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody id="timeTable">
		<?php foreach ($result as $row) { ?>
		<tr>
			<td><?php echo $row['item']; ?></td>
			<td<?php echo $row['status']?'':' class="red"';?>><?php echo $row['status']?"启用":"禁用"; ?></td>
			<td><a href="/xiamenyyhoutai/wechat/editMenu/<?php echo $row['id']; ?>" class="btn btn-green">编辑</a></td>
		</tr>
		<?php foreach ($row['next'] as $v) { ?>
		<tr>
			<td>----<?php echo $v['item']; ?></td>
			<td<?php echo $v['status']?'':' class="red"';?>><?php echo $v['status']?"启用":"禁用"; ?></td>
			<td><a href="/xiamenyyhoutai/wechat/editMenu/<?php echo $v['id']; ?>" class="btn btn-green">编辑</a></td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>
</div>
</div>