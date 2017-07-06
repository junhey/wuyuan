<?php if ( ! defined('IN_DILICMS')) exit('No direct script access allowed');
/**
 * DiliCMS
 *
 * 一款基于并面向CodeIgniter开发者的开源轻型后端内容管理系统.
 *
 * @package     DiliCMS
 * @author      DiliCMS Team
 * @copyright   Copyright (c) 2011 - 2012, DiliCMS Team.
 * @license     http://www.dilicms.com/license
 * @link        http://www.dilicms.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * DiliCMS 内容模型管理和内容模型字段管理控制器
 *
 * @package     DiliCMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Jeongee
 * @link        http://www.dilicms.com
 */
class Model extends Admin_Controller
{
	/**
     * model
     * 缓存model的ID
     *
     * @var int
     * @access  private
     **/
	private $model = 0; 
	
	/**
     * 构造函数
     *
     * @access  public
     * @return  void
     */
	public function __construct()
	{
		parent::__construct();
		$this->_check_permit();
		$this->load->model('model_mdl');
        $this->load->helper('thumb');
	}
	
	/**
     * 内容模型默认入口（列表页）
     *
     * @access  public
     * @return  void
     */					
	public function view()
	{
		$data['list'] = $this->model_mdl->get_models();
		$data['bread'] = make_bread(Array(
			'模型管理' => '',
			'内容模型管理' => site_url('model/view'),
		));
		$this->_template('model_list', $data);
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型添加表单页入口
     *
     * @access  public
     * @return  void
     */
	public function add()
	{
		$this->_add_post();
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型添加表单呈现/处理函数
     *
     * @access  public
     * @return  string
     */
	public function _add_post()
	{
		if ($this->_validate_model_form() == TRUE)
		{
			//获取表单数据
			$data['name'] = $this->input->post('name', TRUE);
			$data['description'] = $this->input->post('description', TRUE);
			$data['perpage'] = $this->input->post('perpage', TRUE);
			$data['hasattach'] = $this->input->post('hasattach', TRUE);
            //处理缩略图设置
            create_thumb_preferences($data);
			//新增内容模型
			$this->model_mdl->add_new_model($data);
			//更新缓存
			update_cache('model', $data['name']);
			update_cache('menu');
			
			$this->_message('内容模型添加成功!', 'model/view', TRUE);
		}
		else
		{
			$data['bread'] = make_bread(Array(
				'模型管理' => '',
				'内容模型管理' => site_url('model/view'),
				'添加内容模型' => '',
			));
			$this->_template('model_add', $data);
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型修改页入口
     *
     * @access  public
     * @param   int
     * @return  string
     */
	public function edit($id = 0)
	{
		$this->_edit_post($id);
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型删除入口
     *
     * @access  public
     * @param   int
     * @return  string
     */
	public function del($id = 0)
	{
		$model = $this->model_mdl->get_model_by_id($id);
		if ($model)
		{
			$this->model_mdl->del_model($model);
			update_cache('menu');
			$this->_message('内容模型删除完成！', 'model/view', TRUE);
		}
		else
		{
			$this->_message('不存在的内容模型!' , '', FALSE);	
		}
		
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型修改表单显示/处理函数
     *
     * @access  public
     * @param   int
     * @return  string
     */
	public function _edit_post($id = 0)
	{
		$target_model = $this->model_mdl->get_model_by_id($id);
		! $target_model AND $this->_message('不存在的内容模型!', '', FALSE);
		if ($this->_validate_model_form($target_model->name) == TRUE)
		{
			$old_table_name = $target_model->name;
			$data['name'] = $this->input->post('name', TRUE);
			$data['description'] = $this->input->post('description', TRUE);
			$data['perpage'] = $this->input->post('perpage', TRUE);
			$data['hasattach'] = $this->input->post('hasattach', TRUE);
            //处理缩略图设置
            create_thumb_preferences($data);
			$this->model_mdl->edit_model($target_model, $data);
			update_cache('model', $data['name']);
			update_cache('menu');
			$this->_message('内容模型修改成功!', 'model/edit/' . $target_model->id, TRUE);
		}
		else
		{
			$data['model'] = $target_model;
            $data['model']->thumb_preferences = json_decode($target_model->thumb_preferences);
			$data['bread'] = make_bread(Array(
				'模型管理' => '',
				'内容模型管理' => site_url('model/view'),
				'编辑 :: ' . $target_model->description => '',
			));
			$this->_template('model_edit', $data);
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 检测内容模型名称合法性
     *
     * @access  private
     * @param   string
     * @return  bool
     */
	private function _validate_model_form($name = '')
	{
		
		$this->load->library('form_validation');
		$callback = '|callback__check_model_name';
		if ($name AND $name == trim($this->input->post('name', TRUE)) )
		{
			$callback = '';
		}
		$this->form_validation->set_rules('name', '内容模型标识', 'trim|required|alpha_dash|min_length[3]|max_length[20]' . $callback);
		$this->form_validation->set_rules('description', '内容模型名称', 'trim|required|max_length[40]');
		$this->form_validation->set_rules('perpage', '每页显示条数', 'trim|required|integer');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('form');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 检测内容模型名称是否已经存在
     *
     * @access  public
     * @param   string
     * @return  bool
     */
	public function _check_model_name($name = '')
	{
		if ($this->model_mdl->get_model_by_name($name))
		{
			$this->form_validation->set_message('_check_model_name', '已经存在的内容模型标识！');	
			return FALSE;
		}
		else
		{
			return TRUE;	
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段管理默认入口
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function fields($id = 0)
	{
		$data['model'] = $this->model_mdl->get_model_by_id($id);
		! $data['model'] AND $this->_message('不存在的内容模型!', '', FALSE);
		$data['list']  = $this->model_mdl->get_model_fields($id);
		$data['bread'] = make_bread(Array(
			'模型管理' => '',
			'内容模型管理' => site_url('model/view'),
			$data['model']->description => '',
		));
		$this->settings->load('fieldtypes');
		$this->load->library('form');
		$this->_template('fields_list', $data);
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段添加表单入口
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function add_field($id = 0)
	{
		$this->_add_field_post($id);
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段添加表单呈现/处理函数
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function _add_field_post($id = 0)
	{
		$data['model'] = $this->model_mdl->get_model_by_id($id);
		! $data['model'] AND $this->_message('不存在的内容模型!', '', FALSE);
		$this->settings->load('fieldtypes');
		if ( ! $this->_validate_field_form($id))
		{
			$data['bread'] = make_bread(Array(
				'模型管理' => '',
				'内容模型管理' => site_url('model/view'),
				$data['model']->description => site_url('model/fields/' . $data['model']->id),
				'添加字段' => '',
			));
			$this->_template('fields_add', $data);
		}
		else
		{
			$this->model_mdl->add_field($data['model'], $this->_get_post_data());
			
			update_cache('model', $data['model']->name);
			
			$this->_message('内容模型字段添加成功!', 'model/fields/' . $id, TRUE);
		}
			
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段修改表单入口
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function edit_field($id = 0)
	{
		$this->_edit_field_post($id);
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段修改表单呈现/处理函数
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function _edit_field_post($id = 0)
	{
		$data['field'] = $this->model_mdl->get_field_by_id($id);
		! $data['field'] AND $this->_message('不存在的内容字段!', '', FALSE);
		$data['model'] = $this->model_mdl->get_model_by_id($data['field']->model);
		! $data['model'] AND $this->_message('不存在的内容模型!', '', FALSE);
		$this->settings->load('fieldtypes');
		if ($this->_validate_field_form($data['field']->model, $data['field']->name))
		{
			$this->model_mdl->edit_field($data['model'],$data['field'], $this->_get_post_data());
			
			update_cache('model', $data['model']->name);
			
			$this->_message('内容模型字段修改成功!', 'model/edit_field/' . $id, TRUE);		
		}
		else
		{	
			$data['bread'] = make_bread(Array(
				'模型管理' => '',
				'内容模型管理' => site_url('model/view'),
				$data['model']->description => site_url('model/fields/' . $data['model']->id),
				'编辑字段' => '',
			));
			$this->_template('fields_edit', $data);	
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 内容模型字段删除入口
     *
     * @access  public
     * @param   int
     * @return  void
     */
	public function del_field($id = 0)
	{
		$field = $this->model_mdl->get_field_by_id($id);
		! $field AND $this->_message('不存在的内容字段!', '', FALSE);
		$model = $this->model_mdl->get_model_by_id($field->model);
		! $model AND $this->_message('不存在的内容模型!', '', FALSE);
		if ($field AND $model)
		{
			
			$this->model_mdl->del_field($model, $field);
			
			update_cache('model', $model->name);
		}
		$this->_message('字段删除成功!', 'model/fields/' . $model->id, TRUE);	
	}
	
	// ------------------------------------------------------------------------

    /**
     * 检查内容模型字段是否已经存在
     *
     * @access  public
     * @param   string
     * @return  bool
     */
	public function _check_field_name($name)
	{
		if ($this->model_mdl->check_field_unique($this->model, $name))
		{
			$this->form_validation->set_message('_check_field_name', '已经存在的字段标识！');	
			return FALSE;
		}
		else
		{
			return TRUE;	
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 检查内容模型字段是否与保留字冲突
     *
     * @access  public
     * @param   string
     * @return  bool
     */
	public function _check_field_name_valid($name)
	{
		if ($name == 'id' OR $name == 'create_time' OR $name == 'update_time')
		{
			$this->form_validation->set_message('_check_field_name_valid', '字段标识不能为id或者create_time或者update_time！');	
			return FALSE;
		}
		else
		{
			return TRUE;	
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * 检查内容模型字段合法性
     *
     * @access  private
     * @param   string
     * @return  bool
     */
	private function _validate_field_form($model = 0, $name = '')
	{
		$this->model = $model;
		$this->load->library('form_validation');
		$callback = '|callback__check_field_name';
		if ($name AND $name == trim($this->input->post('name')) )
		{
			$callback = '';
		}
		$this->form_validation->set_rules('name', '字段标识', 'trim|required|alpha_dash|min_length[3]|max_length[20]|callback__check_field_name_valid' . $callback);
		$this->form_validation->set_rules('description', '字段名称', 'trim|required|max_length[40]');
		$this->form_validation->set_rules('type', '字段类型', 'trim|required');
		$this->form_validation->set_rules('length', '字段长度', 'trim');
		$this->form_validation->set_rules('values', '数据源', 'trim');
		$this->form_validation->set_rules('width', '宽度', 'trim|integer');
		$this->form_validation->set_rules('height', '高度', 'trim|integer');
		$this->form_validation->set_rules('width', '宽度', 'trim|integer');
		$this->form_validation->set_rules('order', '显示顺序', 'trim|integer');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('form');
			return FALSE;
		}
		else
		{
			return TRUE;
		}	
	}
	
	// ------------------------------------------------------------------------

    /**
     * 获取并处理内容模型字段表单信息
     *
     * @access  private
     * @return  array
     */
	private function _get_post_data()
	{
		$data['name'] = $this->input->post('name', TRUE);
		$data['description'] = $this->input->post('description', TRUE);
		$data['type'] = $this->input->post('type', TRUE);
		$data['length'] = $this->input->post('length', TRUE);
		$data['values'] = $this->input->post('values', TRUE);
		$data['width'] = $this->input->post('width', TRUE) ? $this->input->post('width', TRUE) : 0;
		$data['height'] = $this->input->post('height', TRUE) ? $this->input->post('height', TRUE) : 0;
		$data['rules'] = $this->input->post('rules', TRUE);
		$data['ruledescription'] = $this->input->post('ruledescription', TRUE);
		$data['searchable'] = $this->input->post('searchable', TRUE);
		$data['listable'] = $this->input->post('listable', TRUE);
		$data['editable'] = $this->input->post('editable', TRUE);
		$data['order'] = $this->input->post('order', TRUE) ? $this->input->post('order', TRUE) : 0 ;
		if ($data['rules'] AND is_array($data['rules']))
		{
			$data['rules'] = implode(',', $data['rules']);	
		}
		else
		{
			$data['rules'] = '';
		}
		return $data;	
	}

	// ------------------------------------------------------------------------

}

/* End of file model.php */
/* Location: ./admin/controllers/model.php */