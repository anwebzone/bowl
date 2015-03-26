<?php
namespace Anax\MVC;

/*
 * Model for Users
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
	/**
	 * Get the table name.
	 *
	 * @return string with the table name.
	 */
	public function getSource()
	{
			return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
	}
	
	/**
	 * Set prefix to use infront of table name.
	 *
	 */
	public function setTablePrefix($prefix)
  {
        $this->db->setTablePrefix($prefix);
  }
	
	/**
	 * Build a select query.
	 *
	 */
	public function query($columns = '*')
	{
			$this->db->select($columns)
							 ->from($this->getSource());
							 
			return $this;
	}
	
	/**
	 * Build the where part.
	 *
	 */
	public function where($condition)
	{
			$this->db->where($condition);
			
			return $this;
	}
	
	/**
	 * Build the andWhere part.
	 *
	 */
	public function andWhere($condition)
	{
			$this->db->andWhere($condition);
			
			return $this;
	}
	
	/**
	 * Build the limit part.
	 *
	 */
	public function limit($condition)
	{
			$this->db->limit($condition);
			
			return $this;
	}
	
	/**
	 * Build the offset part.
	 *
	 */
	public function offset($condition)
	{
			$this->db->offset($condition);
			
			return $this;
	}
	
	/**
	 * Build the orderBy part.
	 *
	 */
	public function orderBy($condition)
	{
			$this->db->orderBy($condition);
			
			return $this;
	}
	
	/**
	 * Execute the query built.
	 *
	 */
	public function execute($params = [])
	{
			$this->db->execute($this->db->getSQL(), $params);
			$this->db->setFetchModeClass(__CLASS__);
			
			return $this->db->fetchAll();
	}
	
	/**
	 * Find and return all.
	 *
	 * @return array
	 */
	public function findAll()
	{
			$this->db->select()
							 ->from($this->getSource());
	 
			$this->db->execute();
			$this->db->setFetchModeClass(__CLASS__);
			return $this->db->fetchAll();
	}
	
	/**
	 * Find by id
	 *
	 */
	public function find($id)
	{
			$this->db->select()
							 ->from($this->getSource())
							 ->where('id = ?');
			
			$this->db->execute([$id]);
			return $this->db->fetchInto($this);
		
	}
	
	/**
	 * Create new row.
	 *
	 */
	public function create($values)
	{
			$keys   = array_keys($values);
			$values = array_values($values);
			
			$this->db->insert(
				$this->getSource(),
				$keys
			);
			
			$res = $this->db->execute($values);
			$this->id = $this->db->lastInsertId();
			
			return $res;
	}
	
	/**
	 * Update row.
	 *
	 */
	public function update($values)
	{
			$keys   = array_keys($values);
			$values = array_values($values);
			
			unset($keys['id']);
			$values[] = $this->id;
			
			$this->db->update(
					$this->getSource(),
					$keys,
					"id = ?"
			);
			
			return $this->db->execute($values);
	}
	
	/**
	 * Save current object/row.
	 *
	 */
	public function save($values = [])
	{
			$this->setProperties($values);
			$values = $this->getProperties();
			
			if(isset($values['id'])) {
					return $this->update($values);
			}
			else {
					return $this->create($values);
			}	
	}
	
	/**
	 * Delete by id.
	 *
	 */
	public function delete($id)
	{
			$this->db->delete(
					$this->getSource(),
					'id = ?'
			);
			
			return $this->db->execute([$id]);
	}
	
	/** 
	 * Inner join table
	 *
	 */
	public function join($table, $condition)
	{
			$this->db->join($table, $condition);
			
			return $this;
	}
	
  /** 
	 * Left join table
	 *
	 */
	public function leftJoin($table, $condition)
	{
			$this->db->leftJoin($table, $condition);
			
			return $this;
	}
	
	/**
	 * Group by
	 *
	 */
	public function groupBy($condition)
	{
			$this->db->groupBy($condition);
			
			return $this;
	}
	
	
	/**
	 * Set object properties.
	 *
	 */
	public function setProperties($properties)
	{
			if(!empty($properties)) {
					foreach($properties AS $key => $val) {
							$this->$key = $val;
					}
			}
	}
	
	/**
	 * Get object properties.
	 *
	 */
	public function getProperties()
	{
			$properties = get_object_vars($this);
			unset($properties['di'], $properties['db']);
			
			return $properties;
	}

}