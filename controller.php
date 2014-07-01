<?PHP 
require_once('api/SCpro.php');
if(is_file(__DIR__ . '/model.php')){
    include_once(__DIR__ . '/model.php');
}

########################################
class KQPositionCollector extends SCpro
{
  private $copyrights;
  public function __construct($type=''){
    if(class_exists('KQPCModel')){
        $this->copyrights = new KQPCModel();
    }else{
        die('Cannot find model to Copyright plugin!');
    }
    
    /*if($type == 'onAdmin' && isset($_GET['module']) && $_GET['module']=='ProductAdmin' && isset($_GET['id'])){
        if ($product = $this->products->get_product($this->request->get('id', 'integer'))){
            $visible = ($product->visible) ? ' checked' : '';
            $html ='<div class="block layer">
                        <h2>Описание задачи копирайтеру</h2>
                        <ul>
                            <li><label class="property">Описание задачи</label></li>
                		    <textarea name="task_desc" class="editor_small">'.addslashes($product->task_desc).'</textarea>
                        </ul>
                        <ul>
                            <li><label class="property">Ключевые слова</label><input name="keywords" class="scpro_inp" type="text" value="'.addslashes($product->keywords).'"/></li>
                        </ul>
                		<ul>
                            <li><label class="property">Стоимость задачи</label><input name="task_cost" class="scpro_inp" type="text" value="'.addslashes($product->task_cost).'"/></li>
                        </ul>
                    	<div class="checkbox">
                            <input name=visible value=\'1\' type="checkbox" id="active_checkbox"'.$visible.'/> <label for="active_checkbox">Активировать задачу копирайтеру</label>
                        </div>
                    </div>';

            $this->addJqueryEvent('.block:has(#properties_wizard)', 'before', $html);
        }
    }*/
    
    if($type == 'onSite'){
        $user = (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) ? true : false;
        if($user){
            /*$page = null;
            if($this->request->get('page_url', 'string') && strpos($_GET['page_url'], '-rabota')){
                $url = $_GET['page_url'] = str_replace('-rabota', '', $_GET['page_url']);
                $page = $this->pages->get_page($url, true);
                $page_type = (isset($page->page_type)) ? $page->page_type : 'page';
                $page_path = $url;
            }elseif($this->request->get('product_url', 'string') && strpos($_GET['product_url'], '-rabota')){
                $url = $_GET['product_url'] = str_replace('-rabota', '', $_GET['product_url']);
                $page = $this->products->get_product($url);
                $page_type = 'products';
                $page_path = 'products/' . $url;
            }elseif($this->request->get('category', 'string') && strpos($_GET['category'], '-rabota')){
                $url = $_GET['category'] = str_replace('-rabota', '', $_GET['category']);
                $page = $this->categories->get_category($url);
                $page_type = 'catalog';
                $page_path = 'catalog/' . $url;
            }elseif($this->request->get('url', 'string') && $this->request->get('module', 'string') == 'BlogView' && strpos($_GET['url'], '-rabota')){
                $url = $_GET['url'] = str_replace('-rabota', '', $_GET['url']);
                $page = $this->blog->get_post($url);
                $page_type = 'blog';
                $page_path = 'blog/' . $url;
            }*/
            $url = false;
            $current_url = explode("?", $_SERVER['REQUEST_URI']);
            if(strpos($current_url[0], '-rabota') !== false){
                $url = str_replace('-rabota', '', $current_url[0]);
                if(isset($_GET['page_url'])) $_GET['page_url'] = str_replace('-rabota', '', $_GET['page_url']);
                if(isset($_GET['product_url'])) $_GET['product_url'] = str_replace('-rabota', '', $_GET['product_url']);
                if(isset($_GET['category'])) $_GET['category'] = str_replace('-rabota', '', $_GET['category']);
                if(isset($_GET['url'])) $_GET['url'] = str_replace('-rabota', '', $_GET['url']);
            }
            
            
            if($url){
                $page = json_decode(file_get_contents("http://siteconstructor.pro/lk_api/copyright.php?action=get_copyright_task&domain={$_SERVER['SERVER_NAME']}&url=$url"));
                if($page){
                    if($this->request->method('post') && $this->request->post('copyright')) {
            			$copyright->name = $this->request->post('name');
            			$copyright->text = $this->request->post('text');
            			$copyright->title = $this->request->post('title');
            			$copyright->task_cost = $this->request->post('task_cost');
            			$copyright->solution_stat = $this->request->post('solution_stat');
            			$copyright->payment_stat = $this->request->post('payment_stat');
            			$copyright->keywords = $this->request->post('keywords');
            			$copyright->description = $this->request->post('description');
            			$copyright->email = $this->request->post('email');
            
                        // Передадим комментарий обратно в шаблон - при ошибке нужно будет заполнить форму
            			$this->design->assign('copyright_task_cost', $copyright->task_cost);
            			$this->design->assign('copyright_title', $copyright->title);
            			$this->design->assign('copyright_text', $copyright->text);
            			$this->design->assign('copyright_name', $copyright->name);
            			$this->design->assign('copyright_solution_stat', $copyright->solution_stat);
            			$this->design->assign('copyright_payment_stat', $copyright->payment_stat);
            			$this->design->assign('copyright_keywords', $copyright->keywords);
            			$this->design->assign('copyright_description', $copyright->description);
            			$this->design->assign('copyright_email', $copyright->email);
            
            
                        $keywords = explode(',', $page->keywords);
                        $count_kw = 0;
                        foreach($keywords as $keyword){
                            $count_kw += substr_count($copyright->text, trim($keyword));
                        }
                        
                        $ptrn = '~(([a-z]+)([а-яёЁ]+)|([а-яёЁ]+)([a-z]+))~iu';
            
                        if (empty($copyright->text)) {
                            $this->design->assign('error', 'empty_copyright');
                        }
                        elseif (strlen($copyright->text) < $page->text_count) {
                            $this->design->assign('error', 'count_text');
                        }
                        elseif ($count_kw < $page->key_count) {
                            $this->design->assign('error', 'count_keys');
                        }
                        elseif (preg_match($ptrn, $copyright->text)) {
                            $this->design->assign('error', 'sub_cyrilic');
                        }
                        else {
                            // Создаем комментарий
                            $copyright->object_id = $page->id;
                            $copyright->type = $page_type;
                            $copyright->url = $page_path;
                            $copyright->ip = $_SERVER['REMOTE_ADDR'];
            
                            // Если были одобренные комментарии от текущего ip, одобряем сразу
                            $this->db->query("SELECT 1 FROM __copyrights WHERE approved=0 AND ip=? LIMIT 1", $copyright->ip);
                            if ($this->db->num_rows() > 0)
                                $copyright->approved = 0;
                            
                            // Добавляем комментарий в базу
                            $copyright_id = $this->copyrights->add_copyright($copyright);
                            $this->design->assign('msg', 'Спасибо! Копирайт отправлен на обработку');
                        }
                    }
            		$copyrights = $this->copyrights->get_copyrights(array('type'=>$page_type, 'object_id'=>$page->id, 'approved'=>0, 'limit'=>1, 'ip'=>$_SERVER['REMOTE_ADDR']));
            
            		$addcopyright = 0;        		
            		if (!empty($copyrights))
            		{
            			$this->design->assign('copyrights_name', $copyrights[0]->name);
            			$this->design->assign('copyrights_text', $copyrights[0]->text);
            			/*if ($copyrights[0]->approved == 1)
            			{		
            				$addcopyright = 1;
            			}*/
            		}
                    $this->design->assign('addcopyright', $addcopyright);
            		$this->design->assign('copyrights', $copyrights);
                    $this->design->assign('page', $page);
                    
                    $form = $this->design->fetch(__DIR__.'/views/form.tpl');
                    $this->addJqueryEvent('#content', 'append', $form);
                }
            }
            
            $catalog = $this->categories->get_categories_tree(',(c.RST + c.RNT_int + c.K_int + c.OS_int + c.PG_int) as task,COUNT(cr.object_id) as cr_item', 'LEFT JOIN __copyrights AS cr ON c.id=cr.object_id GROUP BY cr.object_id');
            $this->fillItemsToCatalog($catalog);
            $this->design->assign('catalog', $catalog);
            
            $c = $this->pages_categories->get_categories_tree(1, 'pc.id,pc.parent_id,pc.url,pc.name,pc.visible,(pc.RST + pc.RNT_int + pc.K_int + pc.OS_int + pc.PG_int) as task,c.object_id as cr_item', 'LEFT JOIN __copyrights AS c ON pc.id=c.object_id');
            $this->fillPagesToCategories($c);
            $categories = array(
                'categories' => $c,
                'pages' => $this->pages->get_pages(array('category_id' => 0, 'menu_id' => 1), 'p.id, p.url, p.name, p.header, p.visible,(p.RST + p.RNT_int + p.K_int + p.OS_int + p.PG_int) as task,c.object_id as cr_item', false, 'LEFT JOIN __copyrights AS c ON p.id=c.object_id')
            );
            $this->design->assign('categories', $categories);
            
            $left_menu = $this->design->fetch(__DIR__.'/views/tree_menu.tpl');
            $this->addJqueryEvent('#left', 'append', $left_menu);
        }
    }
  }

  function fetch()
  {
$this->runAspect(__CLASS__, __FUNCTION__, 'pre' ); 
  	//Database::connect(die('test'));
 	$filter = array();  	
    
    $status = $this->request->get('status', 'integer');
 	$this->design->assign('status', $status);

    if($status == 1){
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
  		
      	$filter['limit'] = 40;
     
        // Тип
        $type = $this->request->get('type', 'string');
        if($type)
        {
        	$filter['type'] = $type;
     		$this->design->assign('type', $type);
     	}
    
        // Поиск
      	$keyword = $this->request->get('keyword', 'string');
      	if(!empty($keyword))
      	{
    	  	$filter['keyword'] = $keyword;
     		$this->design->assign('keyword', $keyword);
    	}
        
        // Обработка действий 	
      	if($this->request->method('post'))
      	{
    		
                // Действия с выбранными
        		$ids = $this->request->post('check');
        		if(!empty($ids) && is_array($ids))
        		switch($this->request->post('action'))
        		{
        		    case 'approve':
        		    {
        				foreach($ids as $id)
        					$this->copyrights->update_copyright($id, array('approved'=>1));    
        		        break;
        		    }
        		    case 'delete':
        		    {
        				foreach($ids as $id)
        					$this->copyrights->delete_copyright($id);    
        		        break;
        		    }
        		}
            
     	}
    
      
    
    	// Отображение
      	$copyrights_count = $this->copyrights->count_copyrights($filter);
    	// Показать все страницы сразу
    	if($this->request->get('page') == 'all')
    		$filter['limit'] = $copyrights_count;	
      	$copyrights = $this->copyrights->get_copyrights($filter, true);
      	
      	// Выбирает объекты, которые прокомментированы:
      	$products_ids = array();
      	$posts_ids = array();
      	foreach($copyrights as $copyright)
      	{
      		if($copyright->type == 'product')
      			$products_ids[] = $copyright->object_id;
      		if($copyright->type == 'blog')
      			$posts_ids[] = $copyright->object_id;
      	}
    	$products = array();
    	foreach($this->products->get_products(array('id'=>$products_ids)) as $p)
    		$products[$p->id] = $p;
    
      	$posts = array();
      	foreach($this->blog->get_posts(array('id'=>$posts_ids)) as $p)
      		$posts[$p->id] = $p;
    
    
      	foreach($copyrights as &$copyright)
      	{
      		if($copyright->type == 'product' && isset($products[$copyright->object_id]))
      			$copyright->product = $products[$copyright->object_id];
      		if($copyright->type == 'blog' && isset($posts[$copyright->object_id]))
      			$copyright->post = $posts[$copyright->object_id];	
      	}
      	
      	if ($_GET['type'] == 'opl')
    	{
    		$this->design->assign('check', 'checked');
    	}
    	else
    	{
    		$this->design->assign('check', '');
    	}
    	
    	$module_status = $this->modules->status_module("KQPCollector");
    	print_r($module_status[0]->status);
    	
    
    	$this->design->assign('pages_count', ceil($copyrights_count/$filter['limit']));
     	$this->design->assign('current_page', $filter['page']);
    
     	$this->design->assign('copyrights', $copyrights);
     	$this->design->assign('copyrights_count', $copyrights_count);
        $this->design->assign('base_url', 'http://'.$_SERVER['SERVER_NAME'].'/');
    
    	return $this->design->fetch(__DIR__.'/views/copyrights.tpl');
        
    }else{
        if($this->request->method('post'))
      	{
            // Действия с выбранными
    		$check = $this->request->post('check');
            $data = $this->request->post('data');
    		if(!empty($check) && is_array($check)){
                foreach($check as $type => $ids){
                    if(!empty($ids) && is_array($ids)){
                        foreach($ids as $id){
                            $this->copyrights->update_copyright_page($id, $data[$id], $type);
                        }
                        $this->design->assign('msg', 'Данные успешно сохранены');
                    }
                }
    		}
     	}
        
        
        $this->design->assign('base_url', 'http://'.$_SERVER['SERVER_NAME'].'/');
        $this->design->assign('pages', $this->pages->get_pages());
        $this->design->assign('categories', $this->pages_categories->get_categories());
        
        $posts = array();
        $query = "SELECT * FROM __blog WHERE 1";
        $this->db->query($query);
        foreach ($this->db->results() as $post)
            $posts[$post->id] = $post;
         
        $this->design->assign('posts', $posts);
        
        $products = array();
        $query = "SELECT * FROM __products WHERE 1";
        $this->db->query($query);
        foreach ($this->db->results() as $product)
            $products[$product->id] = $product;
         
        $this->design->assign('products', $products);
        
        $catalogs = array();
        $query = "SELECT * FROM __categories WHERE 1";
        $this->db->query($query);
        foreach ($this->db->results() as $catalog)
            $catalogs[$catalog->id] = $catalog;
         
        $this->design->assign('catalogs', $catalogs);

        return $this->design->fetch(__DIR__.'/views/copyrights_table.tpl');
    }
  }
  
    function fillPagesToCategories(&$categores)
    {
        foreach ($categores as $k => $cat) {
            $categores[$k]->pages = $this->pages->get_pages(array('category_id' => $cat->id), 'p.id, p.url, p.name, p.header, p.visible,(p.RST + p.RNT_int + p.K_int + p.OS_int + p.PG_int) as task,c.object_id as cr_item', false, 'LEFT JOIN __copyrights AS c ON p.id=c.object_id');
            if (isset($cat->subcategories) && $cat->subcategories)
                $this->fillPagesToCategories($cat->subcategories);
        }
    }
    
    function fillItemsToCatalog(&$catalog)
    {
        foreach ($catalog as $k => $cat) {
            $catalog[$k]->pages = $this->products->get_products(array('category_id' => $cat->id), false, false, 'p.keywords,p.task_cost,(p.RST + p.RNT_int + p.K_int + p.OS_int + p.PG_int) as task,cr.object_id as cr_item,', 'LEFT JOIN __copyrights AS cr ON p.id=cr.object_id');
            if (isset($cat->subcategories) && $cat->subcategories)
                $this->fillItemsToCatalog($cat->subcategories);
        }
    }
  
  function turn_on(){
    
    $query = $this->db->placehold("CREATE TABLE IF NOT EXISTS `s_copyrights` (
                                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                      `date` datetime NOT NULL,
                                      `ip` varchar(20) NOT NULL,
                                      `object_id` int(11) NOT NULL,
                                      `name` varchar(255) NOT NULL,
                                      `text` text NOT NULL,
                                      `type` enum('product','blog','page') NOT NULL,
                                      `approved` int(1) NOT NULL,
                                      `email` longtext NOT NULL,
                                      `solution_stat` varchar(255) NOT NULL,
                                      `task_cost` int(11) NOT NULL,
                                      `payment_stat` varchar(255) NOT NULL,
                                      `cop_email` varchar(255) NOT NULL,
                                      `keywords` varchar(255) NOT NULL,
                                      `title` varchar(255) NOT NULL,
                                      `description` varchar(255) NOT NULL,
                                      `url` varchar(200) NOT NULL,
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
    $this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__pages` ADD `task_desc` TEXT NOT NULL ,ADD `keywords` VARCHAR( 500 ) NOT NULL ,ADD `task_cost` INT( 15 ) NOT NULL ,ADD `KN` INT( 11 ) NOT NULL ,ADD `KV` INT( 11 ) NOT NULL ,ADD `RST` TINYINT( 1 ) NOT NULL ,ADD `RNT_int` TINYINT( 1 ) NOT NULL ,ADD `RNT` VARCHAR( 255 ) NOT NULL ,ADD `K_int` TINYINT( 1 ) NOT NULL ,ADD `OS_int` TINYINT( 1 ) NOT NULL ,ADD `PG_int` TINYINT( 1 ) NOT NULL ,ADD `K_meta` VARCHAR( 255 ) NOT NULL ,ADD `OS` VARCHAR( 255 ) NOT NULL ,ADD `PG` VARCHAR( 255 ) NOT NULL ,ADD `text_count` INT( 11 ) NOT NULL ,ADD `key_count` INT( 6 ) NOT NULL ,ADD `update_time` DATETIME NULL");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__pages_categories` ADD `task_desc` TEXT NOT NULL ,ADD `keywords` VARCHAR( 500 ) NOT NULL ,ADD `task_cost` INT( 15 ) NOT NULL ,ADD `KN` INT( 11 ) NOT NULL ,ADD `KV` INT( 11 ) NOT NULL ,ADD `RST` TINYINT( 1 ) NOT NULL ,ADD `RNT_int` TINYINT( 1 ) NOT NULL ,ADD `RNT` VARCHAR( 255 ) NOT NULL ,ADD `K_int` TINYINT( 1 ) NOT NULL ,ADD `OS_int` TINYINT( 1 ) NOT NULL ,ADD `PG_int` TINYINT( 1 ) NOT NULL ,ADD `K_meta` VARCHAR( 255 ) NOT NULL ,ADD `OS` VARCHAR( 255 ) NOT NULL ,ADD `PG` VARCHAR( 255 ) NOT NULL ,ADD `text_count` INT( 11 ) NOT NULL ,ADD `key_count` INT( 6 ) NOT NULL ,ADD `update_time` DATETIME NULL");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__categories` ADD `task_desc` TEXT NOT NULL ,ADD `keywords` VARCHAR( 500 ) NOT NULL ,ADD `task_cost` INT( 15 ) NOT NULL ,ADD `KN` INT( 11 ) NOT NULL ,ADD `KV` INT( 11 ) NOT NULL ,ADD `RST` TINYINT( 1 ) NOT NULL ,ADD `RNT_int` TINYINT( 1 ) NOT NULL ,ADD `RNT` VARCHAR( 255 ) NOT NULL ,ADD `K_int` TINYINT( 1 ) NOT NULL ,ADD `OS_int` TINYINT( 1 ) NOT NULL ,ADD `PG_int` TINYINT( 1 ) NOT NULL ,ADD `K_meta` VARCHAR( 255 ) NOT NULL ,ADD `OS` VARCHAR( 255 ) NOT NULL ,ADD `PG` VARCHAR( 255 ) NOT NULL ,ADD `text_count` INT( 11 ) NOT NULL ,ADD `key_count` INT( 6 ) NOT NULL ,ADD `update_time` DATETIME NULL");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__products` ADD `task_desc` TEXT NOT NULL ,ADD `keywords` VARCHAR( 500 ) NOT NULL ,ADD `task_cost` INT( 15 ) NOT NULL ,ADD `KN` INT( 11 ) NOT NULL ,ADD `KV` INT( 11 ) NOT NULL ,ADD `RST` TINYINT( 1 ) NOT NULL ,ADD `RNT_int` TINYINT( 1 ) NOT NULL ,ADD `RNT` VARCHAR( 255 ) NOT NULL ,ADD `K_int` TINYINT( 1 ) NOT NULL ,ADD `OS_int` TINYINT( 1 ) NOT NULL ,ADD `PG_int` TINYINT( 1 ) NOT NULL ,ADD `K_meta` VARCHAR( 255 ) NOT NULL ,ADD `OS` VARCHAR( 255 ) NOT NULL ,ADD `PG` VARCHAR( 255 ) NOT NULL ,ADD `text_count` INT( 11 ) NOT NULL ,ADD `key_count` INT( 6 ) NOT NULL ,ADD `update_time` DATETIME NULL");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__blog` ADD `task_desc` TEXT NOT NULL ,ADD `keywords` VARCHAR( 500 ) NOT NULL ,ADD `task_cost` INT( 15 ) NOT NULL ,ADD `KN` INT( 11 ) NOT NULL ,ADD `KV` INT( 11 ) NOT NULL ,ADD `RST` TINYINT( 1 ) NOT NULL ,ADD `RNT_int` TINYINT( 1 ) NOT NULL ,ADD `RNT` VARCHAR( 255 ) NOT NULL ,ADD `K_int` TINYINT( 1 ) NOT NULL ,ADD `OS_int` TINYINT( 1 ) NOT NULL ,ADD `PG_int` TINYINT( 1 ) NOT NULL ,ADD `K_meta` VARCHAR( 255 ) NOT NULL ,ADD `OS` VARCHAR( 255 ) NOT NULL ,ADD `PG` VARCHAR( 255 ) NOT NULL ,ADD `text_count` INT( 11 ) NOT NULL ,ADD `key_count` INT( 6 ) NOT NULL ,ADD `update_time` DATETIME NULL");
	$this->db->query($query);
  }
  
  function turn_off(){
    $query = $this->db->placehold("ALTER TABLE `__pages` DROP `task_desc`,DROP `keywords`,DROP `task_cost`,DROP `KN`,DROP `KV`,DROP `RST`,DROP `RNT_int`,DROP `RNT`,DROP `K_int`,DROP `OS_int`,DROP `PG_int`,DROP `K_meta`,DROP `OS`,DROP `PG`,DROP `text_count`,DROP `key_count`,DROP `update_time`;");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__pages_categories` DROP `task_desc`,DROP `keywords`,DROP `task_cost`,DROP `KN`,DROP `KV`,DROP `RST`,DROP `RNT_int`,DROP `RNT`,DROP `K_int`,DROP `OS_int`,DROP `PG_int`,DROP `K_meta`,DROP `OS`,DROP `PG`,DROP `text_count`,DROP `key_count`,DROP `update_time`;");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__categories` DROP `task_desc`,DROP `keywords`,DROP `task_cost`,DROP `KN`,DROP `KV`,DROP `RST`,DROP `RNT_int`,DROP `RNT`,DROP `K_int`,DROP `OS_int`,DROP `PG_int`,DROP `K_meta`,DROP `OS`,DROP `PG`,DROP `text_count`,DROP `key_count`,DROP `update_time`;");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__products` DROP `task_desc`,DROP `keywords`,DROP `task_cost`,DROP `KN`,DROP `KV`,DROP `RST`,DROP `RNT_int`,DROP `RNT`,DROP `K_int`,DROP `OS_int`,DROP `PG_int`,DROP `K_meta`,DROP `OS`,DROP `PG`,DROP `text_count`,DROP `key_count`,DROP `update_time`;");
	$this->db->query($query);
    $query = $this->db->placehold("ALTER TABLE `__blog` DROP `task_desc`,DROP `keywords`,DROP `task_cost`,DROP `KN`,DROP `KV`,DROP `RST`,DROP `RNT_int`,DROP `RNT`,DROP `K_int`,DROP `OS_int`,DROP `PG_int`,DROP `K_meta`,DROP `OS`,DROP `PG`,DROP `text_count`,DROP `key_count`,DROP `update_time`;");
	$this->db->query($query);
  }
}
?>