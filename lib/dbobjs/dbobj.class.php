<?

class Dbobj {

	protected $dbd     = array() ; //data from table
	protected $d       = array() ; //other data, not from table

	/**
	 * Tells whether object equals given object.
	 *
	 * @param self $o
	 *
	 * @return boolean
	 */
	public function equals($o) {
		if(isset($this->id) && isset($o->id)) {
		   	return $this->id == $o->id;
		} else {
			return FALSE;
		}
	}

	public function validateIsDate($field) {
		$val = $this->$field;
		$d   = explode('-',$val);
		if (!(count($d)==3 && checkdate($d[1],$d[2],$d[0]))) 
			return sprintf( t(get_called_class())." ".t("%s has to be a valid date"), t($field));
	}

	public function validateNotEmpty($field) {
		if(self::fieldIsString($field) && '' === trim($this->$field)) 
			return sprintf(t(get_called_class())." ".t("%s can not be empty!"), t($field));
	}

	public function validateNumeric($field) {
		if (!is_numeric($this->$field))
		   return t(get_called_class()." value has to be numeric");
	}

	public function isNew() {
		return !isset($this->id);
	}

	/**
	 * Returns table name of the object.
	 *
	 * @return string
	 */
	public static function tableName() {
		if(property_exists(get_called_class(), 'table')) {
			return static::$table;
		} else {
		    return pluralize(c2u(get_called_class()));
		}
	}

	public static function stringFields() {
		$fieldNames = self::fieldNames();
		foreach($fieldNames as $field) 
			if (self::fieldIsString($field)) return $field;
	}

	private static function fieldIsString($name) {
		return self::isField($name) && self::fieldFormat($name) === '%s';
	}

	private static function fieldIsInt($name) {
		return self::isField($name) && self::fieldFormat($name) === '%d';
	}

	private static function fieldAcceptsNULL($name) {
		return self::fieldIsInt();
	}

	public static function fieldFormat($fieldName) {
		return static::$FIELDS[$fieldName];
	}

	protected static function fieldNames() {
		return array_keys(static::$FIELDS);
	} 

	public static function isField($name) {
		return in_array($name, self::fieldNames());
	}

	protected static function filterFields($fields = array()) {
		$retVal = array();
		foreach($fields as $fieldName) {
			$retVal[] = self::isField(end(explode('.', $fieldName)));//fieldname may be with alias
		}
		return $retVal;
	}

	public function __set($name, $val) {
		if (self::isField($name)) { 
			$this->dbd[$name] = $val;
		} else {
			$this->d[$name] = $val;
		}
	}

	public function __get($name) {
		if (isset($this->$name)) { 
			if (self::isField($name)) {
				return $this->dbd[$name];
			} else {
				return $this->d[$name];
			}
		}
	}

	public function __isset($name) {
		return isset($this->dbd[$name]) || isset($this->d[$name]);
	}

	private static function fromSQL($data) {
		$d = new static();
		if (!is_array($data)) {
			return NULL;
		} else {
			foreach($data as $field => $value) {
				$field = end(explode('.',$field)); 
				$d->$field = $value;
			}
			return $d;
		}
	}

	public function updateFromForm($data, $fields) {
		if (!$this->isNew()) {
			$updateFields = array();
			$updateData   = array();

			if(is_array($data) && is_array($fields))
				foreach($fields as $field) 
					if (isset($data[$field]) && $this->$field !== $data[$field]) { 
						$updateFields[]     = $field; 
						$updateData[$field] = $data[$field];
					}
			if(count($updateFields) > 0)
				return self::update($this->id, $updateFields, $updateData);
		}
		return FALSE;
	}

	/**
	 *
	 * Creates a new object or modifies existing object with data (usually from form submitted data).
	 *
	 * @return mixed New object or modified object.
	 *
	 * @param $data array Associative array 'data field name' => 'data'
	 *
	 * @param $fields array Names of data fields to copy to the object. 
	 *
	 * @param $obj mixed Object to modify.
	 *
	 * @return self
	 */
	public static function fromForm($data, $fields = NULL, $obj = NULL) {
		$d = (NULL === $obj ? new static() : $obj);
		if (is_array($data)) {
			foreach ($data as $name => $value) {
				if (is_array($fields) && !empty($fields)) {
					if (in_array($name, $fields)) $d->$name = $value;
				} else $d->$name = $value;
			}
		}
		return $d;
	}

	public function delc() {
		if(!$this->isNew()) self::del($this->id);
	}

	public static function del($id) { 
		$query = sprintf("DELETE FROM ".self::tableName()." 
			WHERE id = '%d' LIMIT 1",
			mysql_real_escape_string($id));
		mysql_query($query) or die(t("Record not deleted!") . mysql_error());
	} 

	/**
	 * Deletes rows wich match search conditions.
	 *
	 * @param array $where Array of "field" => "value" pairs.
	 */
	public static function delWhere($where) { 
		if (is_array($where)) {
			$fieldNames = array_keys($where);
			foreach($fieldNames as $field) {
				if (self::isField($field)) {
					$wheres[]  = "$field='".self::fieldFormat($field)."'" ;
					$values[]  = $where[$field];
				}
			}
			$query = vsprintf("
				DELETE FROM ".self::tableName()."
				WHERE ".join(' AND ', $wheres), array_map('mysql_real_escape_string', $values));
			mysql_query($query) or die(t("Records not deleted!") . mysql_error());
		}

	}

	/**
	 * Updates row's data.
	 *
	 * @param integer $id Id of the row to update.
	 *
	 * @param array $fieldNames Names of the fields to update.
	 *
	 * @param array $data Array of "fieldname" => "data".
	 */
	public static function update($id, $fieldNames = array(), $data = array()) {
		$values = array();
		$set    = array();
		if (is_array($fieldNames)) {
			foreach($fieldNames as $field) {
				if (self::isField($field) && isset($data[$field])) {
					if (NULL === $data[$field] && self::fieldAcceptsNULL($field)) 
					{
						$set[] = "$field=%s"; 
						$values[] = 'NULL';
					} else {
						$set[]    = "$field='".self::fieldFormat($field)."'" ;
						$values[] = $data[$field];
					}
				}
			}
		}
		$values[] = (int)$id;//pridedame i sąrašo pabaigą id
		$query = vsprintf("UPDATE ".self::tableName()." SET ".implode(',', $set)." 
				WHERE id='%d' LIMIT 1",
				array_map('mysql_real_escape_string', $values));

		if (mysql_query($query))
		   	return TRUE;
		else 
			self::diem(t("Object not updated!"), $query);
	}

	/**
	 * Puts object data into database table.
	 * 
	 * @return integer|NULL Returns id of the object. NULL if no insert made.
	 */ 
	public function insert() {
		$do_insert = true;
		if (method_exists($this, 'beforeInsert')) {
			$do_insert = $this->beforeInsert();
		} 

		if(false !== $do_insert) {
			$fields  = array();
			$values  = array();
			$formats = array();
			foreach (static::$FIELDS as $field => $format) {
				if (isset($this->$field)) {
					$fields[]  = $field; 
					$values[]  = $this->$field;
					$formats[] = $format;
				}
			}
			$query  = vsprintf("INSERT INTO ".self::tableName()."(".implode(',', $fields).")
					VALUES(".implode(',',array_map('self::quote', $formats)).")", array_map('mysql_real_escape_string', $values));
			mysql_query($query) or die(t("Object not created!") . mysql_error());
			$insId = mysql_insert_id();
		
			if (method_exists($this, 'afterInsert'))
				$this->afterInsert($insId, $this);

			$this->id = $insId; 

			return $insId;
		} else {
			return NULL;
		}
	}

	public static function findBySql($query) {
		if(!$result = mysql_query($query)) {
			debug_print_backtrace();
			die("Query: $query " . t("Object not found!") . mysql_error());
		}

		$objs = array();
		while ($row = mysql_fetch_assoc($result)) { 
			$objs[] = self::fromForm($row);
		}
		return $objs;
	}

	public static function find($filter) {
		return self::findBySql($filter->makeSQL());
	}

	public static function exists($id) {
		if (!is_numeric($id)) return FALSE;
		$cl = get_called_class();
		$filter = self::newFilter(array($cl => array('id')));
		$filter->setFrom(array($cl => 't'));
		$filter->setWhere(array($cl.'id' => (int)$id)); 
		$filter->setLimit(1);
		$obj = self::find($filter);
		return is_array($obj) && count($obj) == 1;
	}

	// Returns _one object_ found in database
	public static function load($id, $fields = array()) {
		$cl = get_called_class();
		$filter = $cl::newFilter(array($cl => "*"));
		$filter->setFrom(array($cl => 't'));
		$filter->setWhere(array("$cl.id" => $id));
		$whats = array();
		if (!empty($fields) && is_array($fields)) {
			foreach($fields as $name)
				$whats[] = $name; //TODO:check if field is valid
			$filter->setWhat(array($cl => $whats));
		}
		$obj = self::find($filter);
		return (is_array($obj) && !empty($obj)) ? $obj[0] : NULL;
	}

	/**
	 * Loads self from database by field data.
	 *
	 * @param array $fv Associative array 'field name' => 'value'.
	 * 
	 * @return self
	 */
	public static function loadBy($fv) {
		$filter = self::newFilter();
		$filter->setWhere($fv);
		$filter->setLimit(1);
		return current(self::find($filter));
	}

	public static function unionFilters($filters) { 
		$sqls = array();
		foreach ($filters as $f) {
			$tmp_filter = $f; 
			$tmp_filter->setLimit(NULL);
			$sqls[] = $tmp_filter->makeSQL();
		}
		$query = '('.join(' UNION ', $sqls).') as t1 ';
		return $query;
	}

	// ACCEPTS
	//      1. $filter
	//      2. array($filter, $filter, ...) - when UNION
	public static function cnt($filter) {
		if (is_array($filter)) { //2
			$query = 'SELECT COUNT(*) as cnt FROM '.self::unionFilters($filter);
		} else {
			$values = array();
			if ('' != $filter->getCount() && is_string($filter->what)) {
				$filter->setWhat($filter->getCount().','.$filter->what);
			} else {
				$filter->setWhat(array("COUNT(*)" => 'cnt'));
			}
			$tmp_filter = $filter;
			$tmp_filter->order   = '';
			$tmp_filter->groupBy = '';
			$tmp_filter->limit   = '';
			$tmp_filter->offset  = '';
			$tmp_filter->setLimit(NULL);
			$query = $tmp_filter->makeSQL();
		}
		$result = mysql_query($query) or die($query . t("Objects count failed!"). mysql_error());
		return mysql_fetch_object($result)->cnt;
	} 

	public static function eq($str) {
		return self::quote(self::e($str));
	}

	/**
	 * Escapes string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function e($str) {
		return mysql_real_escape_string($str);
	}

	/**
	 * Escapes string and adds double quotes to it.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function edq($str) {
		return self::dq(self::e($str));
	}

	/**
	 * Adds single-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function q($str) {
		return self::quote($str);
	}

	/**
	 * Adds double-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function dq($str) {
		return '"'.$str.'"';
	}

	/**
	 * Adds single-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function quote($str) {
		return "'$str'";
	}

	/**
	 * Makes new filter.
	 *
	 * @param mixed $what 
	 *
	 * @return Filter
	 */
	public static function newFilter($what = NULL) {
		$cl = get_called_class();
		if (NULL === $what) $what = array($cl => array("*"));
		$filter = new Filter($what);
		$filter->setFrom(array($cl => 't1'));
		return $filter;
	}

	/**
	 * Dies with message and prints executed query wich did not succeed .
	 *
	 * @param string $message A message to output.
	 *
	 * @param string $query The failed query.
	 *
	 * @return void
	 */
	private static function diem($message, $query) {
		die ($message.PHP_EOL. "Mysql error!  ".mysql_error().PHP_EOL."Actual query: ".$query.PHP_EOL);
	}

	/**
	 * Converts time (Unix timestamp) to date time format.
	 *
	 * @param integer $tm Unix timestamp to convert.
	 * 
	 * @return string
	 */
	public static function toDateTime($tm) {
		return self::toDate($tm).' '.self::toTime($tm);
	}

	/**
	 * Converts time (Unix timestamp) to date.
	 *
	 * @param integer $tm Unix timestamp to convert. If not given then asumes $tm = time().
	 * 
	 * @return string
	 */
	public static function toDate($tm=NULL) {
		if (NULL === $tm) $tm = time();
		return date("Y-m-d", $tm);
	}


	/**
	 * Converts time (Unix timestamp) to time only. Default output format is "H:i:s".
	 *
	 * @param integer $tm Unix timestamp to convert. If not given then asumes $tm = time().
	 * 
	 * @return string
	 */
	public static function toTime($tm=NULL) {
		if (NULL === $tm) $tm = time();
		return date("H:i:s", $tm);
	}

	/**
	 * Parses time from string and returns timestamp.
	 *
	 * @param string $field Name of the field containing the value.
	 *
	 * @return integer
	 */
	public function asTime($field) {
		return self::toTime(strtotime($field));
	}

	/**
	 * Returns object data as string. Use for output.
	 *
	 * @return string
	 */
	public function to_s() {
		if(isset($this->id)) return get_called_class().":#".$this->id;
	}

	/**
	 * Checks whether a row with values exists in the table.
	 *
	 * @param array $data Associative array 'field name' => 'data' to use in WHERE clause.
	 *
	 * @return boolean
	 */
	public static function existsBy($data) {
		if(is_array($data)) {
			$cl = get_called_class();
			$filter = self::newFilter();
			$filter->setFrom(array($cl => 't'));
			$where = array();
			foreach($data as $field => $value) 
				if (self::isField($field)) 
					$where["$cl.$field"] = mysql_real_escape_string($value);
			$filter->setWhere($where);
			$filter->setLimit(1);
			$obj = self::find($filter); 
			return count($obj)==1;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns ClassName.field
	 * 
	 * @param string $field Field name.
	 *
	 * @return string
	 */
	public function cl($field) {
		return get_called_class().".$field";
	}

	/**
	 * Sets the field with datetime value.
	 *
	 * @param string $field
	 *
	 * @param integer $time Unix timestamp
	 *
	 * @return void
	 */
	public function setDateTime($field, $time) {
		if(self::isField($field)) $this->$field = self::toDateTime($time);
	}

	/**
	 * Returns serializable data in field=>value array.
	 *
	 * @return array
	 */
	public function sd() {
		$ret    = array();
		$fields = static::serializeableFields(); 
		foreach($fields as $field) 
			if(self::isField($field)) 
				$ret[$field] = $this->$field; 
		return $ret;
	}


	/**
	 * Converts object to Jobj.
	 *
	 * @return Jobj 
	 */
	public function to_jobj($mapping = NULL) {
		$sd = array();
		if(NULL !== $mapping) {
			foreach($mapping as $from => $to) 
				$sd[$to] = $this->$from;
		} else {
			$sd = $this->sd();
		}
		return new Jobj(get_called_class(), $sd);
	}

	/**
	 * Returns array of class' serializable to json fields.
	 *
	 * @return array
	 */
	protected static function serializeableFields() {
		$cl = get_called_class();
		if(method_exists($cl, 'serializeableFields')) {
			return static::fieldNames();
		} else {
			self::fieldNames();
		}
	}
}

