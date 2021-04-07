<div class="panel">
    <div class="breadcrumb">
		<span class='icon icon-home'></span>
		<a href="#">添加菜单</a>
	</div>
    <div class="panel-body">
<form id="form1" name="form1" method="post" action="/xiamenyyhoutai/wechat/addmenusave">
<table class="table1">
  <tr>
    <td align="right">所属：</td>
    <td style="text-align:left;">
    <select name="parentid">
    	<option value="0">一级菜单</option>
    	<?php foreach ($othParent as $v){?>
    	<option value="<?php echo $v['id'];?>"><?php echo $v['item'];?></option>
    	<?php }?>
    </select>    
    </td>
  </tr>
  <tr>
    <td align="right">名称：</td>
    <td style="text-align:left;"><input type="text" name="item" id="item" size="20" /></td>
  </tr> 
  <tr>
    <td align="right">类型：</td>
    <td style="text-align:left;"><input type="radio" name="type" value="click" checked="checked" />点击 <input type="radio" name="type" value="view" />跳转</td>
  </tr>   
  <tr>
    <td align="right">链接/素材ID：</td>
    <td style="text-align:left;"><input type="text" name="linkstr" id="linkstr" size="50" /></td>
  </tr>
  <tr>
    <td align="right">排序：</td>
    <td style="text-align:left;"><input type="text" name="sort" id="sort" size="5" /> (数字小的排前)</td>
  </tr>
  <tr>
	<td align="right">启用：</td>
	<td style="text-align:left;">
	<select name='status'>
	<option value='1'>是</option>
	<option value='0'>否</option>
	</select>
	</td>
  <tr>
  <tr>
    <td colspan="2"><button type="submit" class="btn-x btn-green">添加</button> <button type="reset" class="btn-x btn-green">重置</button></td>
  </tr>
</table>
</form>
</div>
</div>
