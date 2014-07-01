<?php
class KQPCModel extends SCpro
{	
    public function on()
	{
		die(print_r("works"));
	}
	
	public function off()
	{
		print_r("dont works");
	}
	
	// Возвращает комментарий по id
	public function get_copyright($id)
	{
		$query = $this->db->placehold("SELECT c.id, c.object_id, c.name, c.email, c.ip, c.type, c.text, c.date, c.approved, c.solution_stat, c.task_cost, c.payment_stat, c.title, c.keywords, c.description FROM __copyrights c WHERE id=? LIMIT 1", intval($id));

		if($this->db->query($query))
			return $this->db->result();
		else
			return false; 
	}
	
	
	// Возвращает комментарии, удовлетворяющие фильтру
	public function get_copyrights($filter = array())
	{	
		// По умолчанию
		$limit = 0;
		$page = 1;
		$object_id_filter = '';
		$type_filter = '';
		$keyword_filter = '';
		$approved_filter = '';

		$products_fields = '';
        $products_join = '';
		$notopl ='';
		if(!empty($filter['type']))
            if($filter['type'] == 'product')
		    {
                $products_fields = ', p.url, p.name product';
			    $products_join = 'INNER JOIN __products p ON c.object_id=p.id';
		    }
            elseif($filter['type'] == 'blog')
            {
                $products_fields = ', b.url, b.name product';
                $products_join = 'INNER JOIN __blog b ON c.object_id=b.id';
            }
            elseif($filter['type'] == 'catalog')
            {
                $products_fields = ', p.url, p.name product';
                $products_join = 'INNER JOIN __categories p ON c.object_id=p.id';
            }
            elseif($filter['type'] == 'category')
            {
                $products_fields = ', p.url, p.name page';
                $products_join = 'INNER JOIN __pages_categories p ON c.object_id=p.id';
            }
			elseif($filter['type'] == 'article')
            {
                $products_fields = ', b.url, b.name product';
                $products_join = 'INNER JOIN __articles b ON c.object_id=b.id';
            }
			elseif($filter['type'] == 'page')
            {
                $products_fields = ', p.url, p.name page';
                $products_join = 'INNER JOIN __pages p ON c.object_id=p.id';
            }
			elseif($filter['type'] == 'notopl')
            {
				$notopl = "WHERE CONVERT(`payment_stat` USING utf8) LIKE 'Не оплачено'";
            }
			elseif($filter['type'] == 'opl')
            {
				$notopl = "WHERE CONVERT(`payment_stat` USING utf8) LIKE 'Оплачено'";
            }
			

		if(isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if(isset($filter['page']))
			$page = max(1, intval($filter['page']));

		if(isset($filter['ip']))
			$ip = $this->db->placehold("OR c.ip=?", $filter['ip']);
		if(isset($filter['approved']))
			$approved_filter = $this->db->placehold("AND (c.approved=? $ip)", intval($filter['approved']));
			
		if($limit)
			$sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);
		else
			$sql_limit = '';

		if(!empty($filter['object_id']))
			$object_id_filter = $this->db->placehold('AND c.object_id in(?@)', (array)$filter['object_id']);

	//	if(!empty($filter['type']))
	//		$type_filter = $this->db->placehold('AND c.type=?', $filter['type']);

		if(!empty($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				//$keyword_filter .= $this->db->placehold('AND c.name LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.text LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" ');
$keyword_filter .= $this->db->placehold('AND c.name LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.text LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.email LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" '); 
		}

			
		$sort='DESC';
		
		$query = $this->db->placehold("SELECT c.id, c.object_id, c.ip, c.name, c.email, c.text, c.type, c.date, c.text, c.approved, c.solution_stat, c.task_cost, c.payment_stat, c.title, c.keywords, c.description, c.url
										$products_fields FROM __copyrights c $products_join  $notopl $object_id_filter $keyword_filter $approved_filter ORDER BY id $sort $sql_limit");
	
		$this->db->query($query);
		return $this->db->results();
	}
	
	// Количество комментариев, удовлетворяющих фильтру
	public function count_copyrights($filter = array())
	{	
		$object_id_filter = '';
		$type_filter = '';
		$approved_filter = '';
		$keyword_filter = '';

		if(!empty($filter['object_id']))
			$object_id_filter = $this->db->placehold('AND c.object_id in(?@)', (array)$filter['object_id']);

		if(!empty($filter['type']))
			$type_filter = $this->db->placehold('AND c.type=?', $filter['type']);

		if(isset($filter['approved']))
			$approved_filter = $this->db->placehold('AND c.approved=?', intval($filter['approved']));

		if(!empty($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				//$keyword_filter .= $this->db->placehold('AND c.name LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.text LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" ');
$keyword_filter .= $this->db->placehold('AND c.name LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.text LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" OR c.email LIKE "%'.mysql_real_escape_string(trim($keyword)).'%" '); 
		}

		$query = $this->db->placehold("SELECT count(distinct c.id) as count
										FROM __copyrights c WHERE 1 $object_id_filter $type_filter $keyword_filter $approved_filter", $this->settings->date_format);
	
		$this->db->query($query);	
		return $this->db->result('count');

	}
	
	// Добавление комментария
	public function add_copyright($copyright)
	{	
		$query = $this->db->placehold('INSERT INTO __copyrights
		SET ?%,
		date = NOW()',
		$copyright);

		if(!$this->db->query($query))
			return false;

		$id = $this->db->insert_id();
		return $id;
	}
	
	// Изменение комментария
	public function update_copyright($id, $copyright)
	{
		$date_query = '';
		if(isset($copyright->date))
		{
			$date = $copyright->date;
			unset($copyright->date);
			$date_query = $this->db->placehold(', date=STR_TO_DATE(?, ?)', $date, $this->settings->date_format);
		}
		$query = $this->db->placehold("UPDATE __copyrights SET ?% $date_query WHERE id in(?@) LIMIT 1", $copyright, (array)$id);
		$this->db->query($query);
		return $id;
	}
    
    public function update_copyright_page($id, $data, $table='pages')
	{
        if($data['text_count'] == 'other'){
            $data['text_count'] = $data['text_count_other'];
        }
        if($data['task_cost'] == 'other'){
            $data['task_cost'] = $data['task_cost_other'];
        }
        if(!isset($data['RST'])){
            $data['RST'] = 0;
        }
        if(!isset($data['RNT_int'])){
            $data['RNT_int'] = 0;
        }
        if(!isset($data['K_int'])){
            $data['K_int'] = 0;
        }
        if(!isset($data['OS_int'])){
            $data['OS_int'] = 0;
        }
        if(!isset($data['PG_int'])){
            $data['PG_int'] = 0;
        }
        unset($data['task_cost_other']);
        unset($data['text_count_other']);
        $data['task_desc'] = trim($data['task_desc']);
        $query = $this->db->placehold("UPDATE __$table SET ?% , update_time=NOW() WHERE id in(?@) LIMIT 1", $data, (array)$id);      
        $this->db->query($query);
	}

	// Удаление комментария
	public function delete_copyright($id)
	{
		if(!empty($id))
		{
			$query = $this->db->placehold("DELETE FROM __copyrights WHERE id=? LIMIT 1", intval($id));
			$this->db->query($query);
		}
	}	
}
