<?php if ( ! defined('IN_DILICMS')) exit('No direct script access allowed');?>
<div class="headbar">
	<div class="position"><span>系统</span><span>></span><span>用户组管理</span><span>></span><span>修改用户组</span></div>
</div>
<div class="content_box">
	<div class="content form_content">
        <?php echo form_open('role/edit/'.$role->id); ?>
			<table class="form_table">
				<col width="150px" />
				<col />
				<tr>
					<th> 用户组名称：</th>
					<td><?php $this->form->show('name','input','',$role->name); ?><label>*3-20位用户组标识</label><?php echo form_error('name'); ?></td>
				</tr>
                <tr>
					<th> 允许的权限：</th>
					<td>
                    	<ul class="attr_list">
							<?php
							 $role->rights = explode(',',$role->rights);
							 foreach($rights as $key=>$v): ?>
                            <li><label class="attr"><input type="checkbox" <?php echo in_array($key,$role->rights) ? 'checked="checked"' : ''; ?> value="<?php echo $key; ?>" name="right[]"><?php echo $v; ?></label></li>
							<?php endforeach; ?>
                        </ul>
                    </td>
				</tr>
                <tr>
					<th> 允许的内容模型：</th>
					<td>
                    	<ul class="attr_list">
							<?php 
							 $role->models = explode(',',$role->models);
							foreach($models as $key=>$v): ?>
                            <li><label class="attr"><input type="checkbox" <?php echo in_array($key,$role->models) ? 'checked="checked"' : ''; ?> value="<?php echo $key; ?>" name="model[]"><?php echo $v; ?></label></li>
							<?php endforeach; ?>
                        </ul>
                    </td>
				</tr>
                <tr>
					<th> 允许的分类模型：</th>
					<td>
                    	<ul class="attr_list">
							<?php 
							 $role->category_models = explode(',',$role->category_models);
							foreach($category_models as $key=>$v): ?>
                            <li><label class="attr"><input type="checkbox" <?php echo in_array($key,$role->category_models) ? 'checked="checked"' : ''; ?> value="<?php echo $key; ?>" name="category_model[]"><?php echo $v; ?></label></li>
							<?php endforeach; ?>
                        </ul>
                    </td>
				</tr>
                <tr>
					<th> 允许的插件：</th>
					<td>
                    	<ul class="attr_list">
							<?php 
							 $role->plugins = explode(',',$role->plugins);
							foreach($plugins as $key=>$v): ?>
                            <li><label class="attr"><input type="checkbox" <?php echo in_array($key,$role->plugins) ? 'checked="checked"' : ''; ?> value="<?php echo $key; ?>" name="plugin[]"><?php echo $v; ?></label></li>
							<?php endforeach; ?>
                        </ul>
                    </td>
				</tr>
				<tr>
					<th></th>
					<td>
						<button class="submit" type='submit'><span>修改用户组</span></button>
					</td>
				</tr>
			</table>
		<?php echo form_close(); ?>
	</div>
</div>