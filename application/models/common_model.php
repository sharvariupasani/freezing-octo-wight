<?php
class common_model extends CI_Model{
	public function  __construct(){
		parent::__construct();
		$this->load->database();
	}


	/*
	| -------------------------------------------------------------------
	| Select data
	| -------------------------------------------------------------------
	|
	| general function to get result by passing nesessary parameters
	|
	*/
	public function selectData($table, $fields='*', $where='', $order_by="", $order_type="", $group_by="", $limit="", $rows="", $type='')
	{
		$this->db->select($fields);
		$this->db->from($table);
		if ($where != "") {
			$this->db->where($where);
		}

		if ($order_by != '') {
			$this->db->order_by($order_by,$order_type);
		}

		if ($group_by != '') {
			$this->db->group_by($group_by);
		}

		if ($limit > 0 && $rows == "") {
			$this->db->limit($limit);
		}
		if ($rows > 0) {
			$this->db->limit($rows, $limit);
		}


		$query = $this->db->get();

		if ($type == "rowcount") {
			$data = $query->num_rows();
		}else{
			$data = $query->result();
		}

		#echo "<pre>"; print_r($this->db->queries); exit;
		$query->free_result();

		return $data;
	}


	/*
	| -------------------------------------------------------------------
	| Insert data
	| -------------------------------------------------------------------
	|
	| general function to insert data in table
	|
	*/
	public function insertData($table, $data)
	{
		$result = $this->db->insert($table, $data);
		if($result == 1){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}


	/*
	| -------------------------------------------------------------------
	| Update data
	| -------------------------------------------------------------------
	|
	| general function to update data
	|
	*/
	public function updateData($table, $data, $where, $flag =true)
	{
		$this->db->where($where);
		
		foreach ($data as $key=>$val) {
			$this->db->set($key, $val, $flag);
		}

		if($this->db->update($table)){
			return 1;
		}else{
			return 0;
		}
	}

	/*
	| -------------------------------------------------------------------
	| Delere data
	| -------------------------------------------------------------------
	|
	| general function to delete the records
	|
	*/
	public function deleteData($table, $data)
	{
		if($this->db->delete($table, $data)){
			return 1;
		}else{
			return 0;
		}
	}



	/*
	| -------------------------------------------------------------------
	| check unique fields
	| -------------------------------------------------------------------
	|
	*/
	public function isUnique($table, $field, $value,$where = "")
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($field,$value);
		if ($where != "")
			$this->db->where($where);
		$query = $this->db->get();
		$data = $query->num_rows();
		$query->free_result();
		return ($data > 0)?FALSE:TRUE;
	}


	public function getPurchaseById($id)
	{
		$this->db->select("*,c.name as cat_name,p.description as p_description,c.description as c_description,pp.description as pp_description");
		$this->db->from(PURCHASE_PP);
		$this->db->join(PRODUCT_P, "p.id = pp.p_id");
		$this->db->join(CATEGORY_C, "p.cat_id = c.id");
		$this->db->where(array("pp.id"=>$id));

		$query = $this->db->get();
		$purchase = $query->result();
		$query->free_result();
		return ($purchase);
	}

	public function getCustAutoSuggest($tag)
	{
		$this->db->select('c_id,c_fname,c_lname,c_phone');
		$this->db->from(CUSTOMER);
		$this->db->like('c_fname', $tag, 'after');
		$this->db->or_like('c_lname', $tag, 'after');
		$this->db->or_like('c_phone', $tag, 'after');
		$query = $this->db->get();
		$customers = $query->result_array();
		$resCust = array();
		foreach($customers as $customer)
			$resCust[] = '{"c_id":"'.$customer['c_id'].'","value":"' . $customer['c_fname']." ".$customer['c_lname']."(".$customer['c_phone'].")" . '","label":"' . $customer['c_fname']." ".$customer['c_lname']."(".$customer['c_phone'].")" . '"}';

		$resCust = '['.implode(",",$resCust).']';
		return ($resCust);
	}

	public function getProductAutoSuggest($tag)
	{
		$this->db->select('id,name,stock_onhand,price,brand');
		$this->db->from(PRODUCT);
		$this->db->like('name', $tag, 'after');
		$this->db->or_like('brand', $tag, 'after');
		$query = $this->db->get();
		$products = $query->result_array();
		$resPro = array();
		foreach($products as $product)
			$resPro[] = '{"p_id":"'.$product['id'].'","label":"' . $product['name']."-".$product['brand']."(".$product['stock_onhand'].")" . '","value":"' . $product['name']."-".$product['brand']. '","price":"'.$product['price'].'","qty":"'.$product['stock_onhand'].'"}';

		$resPro = '['.implode(",",$resPro).']';
		return ($resPro);
	}

	public function customerTitleById($id)
	{
			$db = $this->db;
			$db->select('CONCAT(c_fname," ",c_lname,"(",c_phone,")") as customer,c_id',false);
			$db->from(CUSTOMER);
			$db->where(array("c_id"=>$id));
			$query = $db->get();
			$customer = $query->result();
			$query->free_result();
			return $customer[0];
	}

	public function updateProductQty($id,$qty,$minus = false)
	{
		$ret = $this->selectData(PRODUCT,"stock_onhand",array("id"=>$id));
		$stock_onhand = $ret[0]->stock_onhand;
		
		if ($minus)
		$qty = (-1 * $qty);

		$stock_onhand = $stock_onhand + $qty;
		
		$data = array("stock_onhand"=>$stock_onhand);
		$ret = $this->updateData(PRODUCT,$data,array("id"=>$id));
	}

	public function addProductToOrder($product,$invoice)
	{
		$data = array("p_id"=>$product["p_id"],
								   "order_type"=>"product",
								   "quantity"=>$product["p_qty"],
								   "net_price"=>$product["p_price"],
								   "in_id"=>$invoice);
		$result = $this->db->insert(ORDER, $data);

		$this->updateProductQty($product["p_id"],$product["p_qty"],true);
	}

	public function addServiceToOrder($service,$invoice)
	{
		$data = array("service_name"=>"s_name",
									"order_type"=>"service",
								   "net_price"=>$product["s_price"],
								   "in_id"=>$invoice);
		$result = $this->db->insert(ORDER, $data);
	}

}
?>
