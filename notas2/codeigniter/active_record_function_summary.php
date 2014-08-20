<?php


/*NOTA: como espacar NOW() */

$my_data = array( 'field_1' => 'data', 'field_2' => 'data' );
$this->db->set( 'created', 'NOW()', FALSE ); //The FALSE tells it not to escape the field! Thats it, job done!
$this->db->insert( 'my_table', $my_data );




	/**
	 * Active Record cheatsheet for Codeigniter
	 * http://codeigniter.com/user_guide/database/active_record.html
	 *
	 * If you intend to write your own queries you can disable Active Record, allowing the core database library and adapter to utilize fewer resources.
	 * To do so set edit config/database.php and set $active_record = FALSE;
	 */

	// 'users' is used as example table

	/** Select, get and limit ---------------------------------------------- */

	// SELECT * FROM (users)
	$this->db->get('users');
	$this->db->from('users')->get();

	// SELECT name, email FROM (users)
	$this->db->select('name,email')->get('users');
	$this->db->select('name,email')->from('users')->get();
	//To prevent automatically escapeing field or table names with backticks set the second and optional parameter of select() method to FALSE

	// SELECT * FROM (users) LIMIT 10
	$this->db->get('users',10);
	$this->db->limit(10)->get('users');
	$this->db->limit(10)->from('users')->get();

	// SELECT * FROM (users) LIMIT 20, 10
	$this->db->get('users',10, 20);
	$this->db->limit(10, 20)->get('users');
	$this->db->limit(10, 20)->from('users')->get();

	// Or all in one method

	//	SELECT * FROM (users) WHERE name = 'Joe'
	$this->db->get_where('users',array('name' => 'Joe'));

	//	SELECT * FROM (users) WHERE name = 'Joe' LIMIT 10
	$this->db->get_where('users',array('name' => 'Joe'), 10);

	// SELECT * FROM (users) WHERE name = 'Joe' LIMIT 20, 10
	$this->db->get_where('users',array('name' => 'Joe'), 10, 20);


	/** Read results ------------------------------------------------------- */

	//If you assigned teh query to a variable it can be used to show the results:
	$query = $this->db->get('users');
	foreach ($query->result() as $row)
	{
		echo $row->name;
		echo $row->email;
	}

	//To get an array instead of an object
	$query = $this->db->get('users');
	foreach ($query->result_array() as $row)
	{
		echo $row['name'];
		echo $row['email'];
	}

	//To check for results
	$query = $this->db->get('users');
	if ($query->num_rows() > 0) foreach ($query->result() as $row)
	{
		echo $row->name;
		echo $row->email;
	}
	//NOTE: The num_rows() metoth belongs tp the $query object, not to $this->db

	//For single row results
	$query = $this->db->get('users',1);
	$row = $query->row();
	echo $row->name;

	//If you want a specific row returned you can submit the row number as a digit in the first parameter
	$query = $this->db->get('users');
	$row = $query->row(5);
	echo $row->name;

	//The same for an array suing functions row_array() instead

	//Also available functions to walk forward/backwards/first/last (they all return an object)
	$row = $query->first_row();
	$row = $query->last_row();
	$row = $query->next_row();
	$row = $query->previous_row();
	// or their equivalent for return an array instead
	$row = $query->first_row('array');
	$row = $query->last_row('array');
	$row = $query->next_row('array');
	$row = $query->previous_row('array');

	//Another functions to work with results
	$query->num_fields(); //The number of FIELDS (columns) returned by the query.
	$query->free_result() //It frees the memory associated with the result and deletes the result resource ID.
	$this->db->count_all('users') //number of rows in a particular table.It doesn't accept restrictors such as where(), or_where(), like(), or_like(), etc.
	$this->db->count_all_results('users'); //number of rows in a particular query.Accept restrictors such as where(), or_where(), like(), or_like(), etc.


	/** Order by and distinct ---------------------------------------------- */

	// SELECT * FROM (users) ORDER BY name
	$this->db->order_by('name')->get('users');

	// SELECT * FROM (users) ORDER BY name DESC
	$this->db->order_by('name','desc')->get('users');

	// SELECT * FROM (users) ORDER BY RAND()
	$this->db->order_by('name','random')->get('users');

	//You can also pass the sting
	// SELECT * FROM (users) ORDER BY age DESC, name ASC
	$this->db->order_by('age desc, name asc')->get('users');

	// SELECT DISTINCT * FROM (users)
	$this->db->distinct()->get('users');


	/** Agregation, group by and having -----------------------------------7 */

	// SELECT MAX(age) AS age FROM (users)
	$this->db->select_max('age')->get('users');
	// Same for: select_min, select_avg, select_sum

	// SELECT country_id, SUM(id) AS id FROM (users) GROUP BY country_id
	$this->db->select('country_id')->select_sum('id')->group_by('country_id')->get('users');

	//By associative array
	// SELECT country_id,name, SUM(id) AS id FROM (users) GROUP BY country_id,name
	$this->db->select('country_id,name')->select_sum('id')->group_by(array('country_id','name'))->get('users');

	//HAVING:
	$this->db->having('user_id = 45');
	$this->db->having('user_id',45);
	$this->db->having('user_id', 45, FALSE); //prevent escaping
	$this->db->having(array('title =' => 'My Title', 'id <' => $id));
	//Same for: or_having

	/** Where and or_where-------------------------------------------------- */

	//By key/value
	// SELECT * FROM (users) WHERE name = 'Joe' AND age > 20 OR id != 0
	$this->db->where('name','Joe')->where('age >', 20)->or_where('id !=', 0)->get('users');

	//By associative array
	// SELECT * FROM (users) WHERE name = 'Joe' AND age > 20
	$this->db->where(array('name' => 'Joe', 'age >' => 20))->get('users');

	//By custom string
	// SELECT * FROM (users) WHERE name = 'Joe' AND age > 20
	$this->db->where("name = $name AND age > $age")->get('users');

	//To prevent automatically escapeing field names with backticks set the third and optional parameter of select() method to FALSE
	$this->db->where('MATCH (field) AGAINST ("value")', NULL, FALSE);


	/** Where_in, where_between -------------------------------------------- */

	// SELECT * FROM (users) WHERE country_id IN (1, 2) OR country_id IN (10, 20)
	$this->db->where_in('country_id',array(1,2))->or_where_in('country_id',array(10,20))->get('users');

	// SELECT * FROM (users) WHERE country_id NOT IN (1, 2) OR country_id NOT IN (10, 20)
	$this->db->where_not_in('country_id',array(1,2))->or_where_not_in('country_id',array(10,20))->get('users');

	// SELECT * FROM (users) WHERE age BETWEEN 1 AND 18 OR age BETWEEN 65 AND 85
	$this->db->where_between('age',1,18)->or_where_between('age',65,85)->get('users');

	// SELECT * FROM (users) WHERE age NOT BETWEEN 1 AND 18 OR age NOT BETWEEN 65 AND 85
	$this->db->where_not_between('age',1,18)->or_where_not_between('age',65,85)->get('users');


	/** Like --------------------------------------------------------------- */

	//By key/value

	// SELECT * FROM (users) WHERE name LIKE '%Joe%'
	$this->db->like('name', 'Joe')->get('users');
	$this->db->like('name', 'Joe', 'both')->get('users');

	// SELECT * FROM (users) WHERE name LIKE 'Joe%'
	$this->db->like('name', 'Joe','after')->get('users');

	// SELECT * FROM (users) WHERE name LIKE '%Joe'
	$this->db->like('name', 'Joe','before')->get('users');

	//Same for: not_like,or_like and or_not_like

	//By associative array
	// SELECT * FROM (users) WHERE surname LIKE '%Doe%'
	$this->db->like(array('name' => 'Joe','surname' => 'Doe'))->get('users');


	/** Join --------------------------------------------------------------- */

	//SELECT * FROM users u JOIN countries c ON u.country_id = c.id
	$this->db->from('users u')->join('countries c', 'u.country_id = c.id')->get();

	//To specify type of join use the third parameter.Options are: left, right, outer, inner, left outer, and right outer.
	// SELECT * FROM users u LEFT JOIN countries c ON u.country_id = c.id
	$this->db->from('users u')->join('countries c', 'u.country_id = c.id','left')->get();

	/** Cache -------------------------------------------------------------- */

	$this->db->start_cache() //This function must be called to begin caching. All Active Record queries of the correct type (see below for supported queries) are stored for later use.
	$this->db->stop_cache() //This function can be called to stop caching.
	$this->db->flush_cache() //This function deletes all items from the Active Record cache.

