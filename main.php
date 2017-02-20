<?php

/*

element1
 element2
 element3
  element4
  element5
 element6
  element4
 element7
 element8
  element3
   element4
   element5

*/

require_once('mysql.php');

class Element {

	public $parent = [];
	public $name;

	public function __construct($parent, $name) {
		$this->parent[] = $parent;
		$this->name = $name;
	}

	public function add(&$parent) {
		$this->parent[] = $parent;
	}

}

class Menu {

	private $bd;
	private $menu = [];
	private $rel = [];
	private $table;

    public function __construct() {
    	$this->bd = Mysql::getInstance();
    	$this->table = $this->bd->query(
    		"select 
    			menu.id, menu.name, parents.parent_id 
    		from 
    			menu 
    		left join 
    			parents 
    		on 
    			menu_id = menu.id 
    		order by 
    			menu.id"
    	);
    }

    private function gen() {
   	
    	foreach($this->table as $key => $val) {
    		$id = $val['id'];
    		$name = $val['name'];
    		$parent = $val['parent_id'];
    		if(!isset($this->menu[$id]))
    			$this->menu[$id] = new Element(NULL, $name);
    		if(empty($parent))
    			$this->rel[] = &$this->menu[$id];
    	}

    	foreach($this->table as $key => $val) {
    		$parent = $val['parent_id'];
    		$id = $val['id'];

    		if(!empty($parent))
    			$this->menu[$parent]->add( $this->menu[$id] );
    	}    	

    }

    public function to_print() {

    	$this->gen();
    	$this->out($this->rel);

    }

    private function tab($num) {
    	for($i = 0; $i < $num; $i++)
    		echo ' ';
    }

    private function out($menu, $num = 0) {

    	foreach($menu as $key => $val) {
    		if(empty($val)) continue;
    		echo $this->tab($num) . $val->name . "\n";
    		if($val->parent)
    			$this->out($val->parent, $num+1);
    	}

    }

}

((new Menu())->to_print());
