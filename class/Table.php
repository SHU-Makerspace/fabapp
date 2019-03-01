<?php

class Table {
	private $table_name;
	private $label;
	private $modal_contents;
	private $columns = array();

	public function __construct($table_name) {
		$table_name = htmlspecialchars($table_name);
		$this->table_name = $table_name;

		global $mysqli;
		if($result = $mysqli->query("SELECT `label`
									 FROM `table_descriptions`
									 WHERE `t_d_id` = '$table_name`;
		")) {
			$this->label = $result->fetch_assoc()->label;
		}

		if($result = $mysqli->query("SELECT `COLUMN_NAME` 
									 FROM `INFORMATION_SCHEMA`.`COLUMNS` 
									 WHERE `TABLE_NAME`='".$table_name."';
		")) {
			while($row = $result->fetch_assoc()) {
				$this->columns[] = $row['COLUMN_NAME'];
			}
		}
	}


	public static function get_prebuild_data($end, $function, $start) {
		if($function === "byHour") {
			$file_name = "TicketsByHour";
			$head = array("HOUR(`t_start`)", "COUNT(*)");
			$statement = "SELECT HOUR(`t_start`), COUNT(*)
		  					FROM `transactions`
		  					WHERE '$start' <= `t_start`
		  					AND `t_start` <= '$end'
		  					GROUP BY HOUR(`t_start`);";
		}
		elseif($function === "byDay") {
			$file_name = "TicketByDay";
			$head = array("DAYNAME(`t_start`)", "COUNT(*)");
			$statement = "SELECT DAYNAME(`t_start`), COUNT(*)
							FROM `transactions`
							WHERE '$start' <= `t_start` 
		  					AND `t_start` <= '$end'
							GROUP BY WEEKDAY(`t_start`);";				
		}
		elseif($function === "byHourDay") {
			$head = array("DAYNAME(`t_start`)", "HOUR(`t_start`),COUNT(*)");
			$file_name = "TicketsByHourForEachDay";
			$statement = "SELECT DAYNAME(`t_start`), HOUR(`t_start`), COUNT(*)
		  					FROM `transactions`
		  					WHERE '$start' <= `t_start` 
		  					AND `t_start` <= '$end'
		  					GROUP BY HOUR(`t_start`), WEEKDAY(`t_start`)
		  					ORDER BY WEEKDAY(`t_start`), HOUR(`t_start`);";
		}
		elseif($function === "byStation") {
			$file_name = "TicketsByStation";
			$head = array("dg_desc", "COUNT(*)");
			$statement = "SELECT `device_group`.`dg_desc`, COUNT(*)
							FROM `transactions`
							JOIN `devices` ON `transactions`.`d_id` = `devices`.`d_id`
							JOIN `device_group` ON `devices`.`dg_id` = `device_group`.`dg_id`
							WHERE '$start' <= `transactions`.`t_start`
							AND `transactions`.`t_start` <= '$end'
							GROUP BY `device_group`.`dg_desc`;";
		}
		elseif($function === "byAccount") {
			$file_name = "TicketsByAccount";
			$head = array("name", "COUNT(*)");
			$statement = "SELECT `accounts`.`name`, COUNT(*)
							FROM `transactions`
							JOIN `acct_charge` ON `transactions`.`trans_id` = `acct_charge`.`trans_id`
							JOIN `accounts` ON `acct_charge`.`a_id` = `accounts`.`a_id`
							WHERE '$start' <= `transactions`.`t_start`
							AND `transactions`.`t_start` <= '$end'
							GROUP BY `accounts`.`name`;";
		}
		elseif($function === "failedTickets") {
			$file_name = "FailedTickets";
			$head = array("COUNT(*)");
			$statement = "SELECT COUNT(*)
							FROM `transactions`
							WHERE `status_id` = 12
							AND '$start' <= `t_start` 
		  					AND `t_start` <= '$end';";
		}
		$params = array();
		$params["file_name"] = $file_name;
		$params["head"] = $head;
		$params["statement"] = $statement;
		return $params;
	}


	public function get_columns() {
		return $this->columns;
	}

	public function get_column_type($column) {

	}

	public function get_times_for_time_period($start, $end) {

	}

	//count
	public function count_items_for_time_period() {

	}

	// if sql way to say datatype (if time, mark as time)
}
?>