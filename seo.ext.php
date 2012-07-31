<?php



class EXT_Seo extends SYS_Model_Database
{

	public $title       = '';
	public $h1          = '';
	public $description = '';
	public $keywords    = '';
	public $body        = '';
	public $id          = '';
	
	public $allow_overwrite = array();
	
	public $table      = 'seo';
	
	public $name        = 'SEO параметры';
	public $add_action  = TRUE;
	public $edit_action = TRUE;
	public $list_action = TRUE;
	public $act_params  = array();
	
	//--------------------------------------------------------------------------
	
	public function init()
	{
		$this->fields['seo'] = array(
			'id' => array(
				'rules' => 'trim|required',
			),
			'title' => array(
				'label' => 'Заголовок страницы (TITLE)',
				'field' => 'input',
				'rules' => 'trim|strip_tags|length[3,255]',
			),
			'h1' => array(
				'label' => 'Заголовок страницы (H1)',
				'field' => 'input',
				'rules' => 'trim|strip_tags|length[3,255]',
			),
			'keywords' => array(
				'label' => 'META Keywords',
				'field' => 'input',
				'rules' => 'trim|strip_tags|max_length[255]',
			),
			'description' => array(
				'label' => 'META Description',
				'field' => 'textarea',
				'rules' => 'trim|strip_tags',
			),
			'body' => array(
				'label' => 'SEO текст (после основного содержания страницы)',
				'field' => 'html',
				'rules' => 'trim',
			),
		);
		
		if ($this->router->component() == 'admin')
		{
			$this->edit_action = NULL;
			return;
		}
		
		$this->db->where('id=?', $this->get_id());
		$seo = $this->get_row();
		
		if ($seo)
		{
			$this->add_action = FALSE;
			foreach ($seo as $k => $v)
			{
				$this->$k = trim($v);
				$this->allow_overwrite[$k] = !$this->$k;
			}
		}
		else
		{
			$this->act_params['id']  = $this->get_id();
			$this->edit_action = FALSE;
			foreach ($this->fields['seo'] as $k => $f)
			{
				$this->$k = NULL;
				$this->allow_overwrite[$k] = TRUE;
			}
		}
	}
	
	//--------------------------------------------------------------------------
	
	public function get_id()
	{
		
		return md5(implode('/', $this->uri->segments));
	}
	
	//--------------------------------------------------------------------------
	
	public function title($val = NULL)
	{
		return $this->render('title', $val);
	}
	
	//--------------------------------------------------------------------------
	
	public function h1($val = NULL)
	{
		return $this->render('h1', $val, '<h1>', '</h1>');
	}
	
	//--------------------------------------------------------------------------
	
	public function keywords($val = NULL)
	{
		return $this->render('keywords', $val, '<meta name="keywords" content="', '" />');
	}
	
	//--------------------------------------------------------------------------
	
	public function description($val = NULL)
	{
		return $this->render('description', $val, '<meta name="description" content="', '" />');
	}
	
	//--------------------------------------------------------------------------
	
	public function body($val = NULL)
	{
		return $this->render('body', $val);
	}
	
	//--------------------------------------------------------------------------
	
	public function render($key, $val = NULL, $prefix = '', $suffix = '')
	{
		if (! empty($this->allow_overwrite[$key]) && $val) $this->$key = $val;
		elseif (!$this->$key && isset($this->template) && !empty($this->template->$key)) $this->$key = $this->template->$key;
		return $prefix . $this->$key . $suffix;
	}
	
	//--------------------------------------------------------------------------
}