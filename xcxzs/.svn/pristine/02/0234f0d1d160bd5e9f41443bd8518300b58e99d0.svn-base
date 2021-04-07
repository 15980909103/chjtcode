<div class="panel">
    <div class="breadcrumb">
		<span class='icon icon-home'></span>
		<a href="#">修改菜单</a>
	</div>
    <div class="panel-body">
<form id="form1" name="form1" method="post" action="/xiamenyyhoutai/wechat/editmenusave">
<table class="table1">
  <input type="hidden" name="id" value="<?php echo $data['id'];?>">
  <tr>
    <td align="right">所属：</td>
    <td style="text-align:left;">
    <select name="parentid">
    	<option value="0">一级菜单</option>
    	<?php foreach ($othParent as $v){?>
    	<option value="<?php echo $v['id'];?>" <?php if($parentid==$v['id']){echo 'selected="selected"';}?>><?php echo $v['item'];?></option>
    	<?php }?>
    </select>    
    </td>
  </tr>
  <tr>
    <td align="right">名称：</td>
    <td style="text-align:left;"><input type="text" name="item" id="item" size="20" value="<?php echo $data['item'];?>" /></td>
  </tr>  
  <tr>
    <td align="right">类型：</td>
    <td style="text-align:left;">
    <input type="radio" name="type" value="click" <?php if($data['type']=='click'){ echo ' checked="checked"'; } ?> />点击 
    <input type="radio" name="type" value="view" <?php if($data['type']=='view'){ echo ' checked="checked"'; } ?> />跳转 
    </td>
  </tr>   
  <tr>
    <td align="right">链接/素材ID：</td>
    <td style="text-align:left;"><input type="text" name="linkstr" id="linkstr" size="50" value="<?php echo $data['linkstr'];?>" /></td>
  </tr>  
  <tr>
    <td align="right">排序：</td>
    <td style="text-align:left;"><input type="text" name="sort" id="sort" size="5" value="<?php echo $data['sort'];?>" /> (数字小的排前)</td>
  </tr>
  <tr>
	<td align="right">启用：</td>
	<td style="text-align:left;">
	<select name='status'>
	<option value='1' <?php if($data['status']){?>selected<?php }?>>是</option>
	<option value='0' <?php if(!$data['status']){?>selected<?php }?>>否</option>
	</select>
	</td>
  </tr>
  <tr>
    <td colspan="2"><button type="submit" class="btn-x btn-green">修改</button> <button type="reset" class="btn-x btn-green">重置</button></td>
  </tr>
</table>
</form>
</div>
</div>
