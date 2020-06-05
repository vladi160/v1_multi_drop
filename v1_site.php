<?php

class V1Site {

	private $sites = [];
	private $main_site_prefix = '';
	private $database = '';
	private $wpdb = null;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->main_site_prefix = $wpdb->base_prefix;
		$this->database = $wpdb->dbname;
	}


	private function get_sites(){
		return get_sites();
	}

	function get_all_tables(){
		return $this->wpdb->get_col('SHOW TABLES FROM ' . $this->database);
	}

	function get_tables_with_prefix($prefix){
		return $this->wpdb->get_col('SHOW TABLES FROM ' . $this->database . ' LIKE "'. $prefix .'%"');
	}

	function get_all_unused_tables(){

		global $wpdb; // WORKS UNDER switch_to_blog

		$sites = $this->get_sites();

		if(!is_array($sites) && count($sites) < 1) return false;

		$tables = $this->get_all_tables();

		$unused_tables = $tables;
		$prefixes = [];



		/*foreach ($tables as $table){
			echo $table;
			if(substr($table, 0, strlen('b69265_wp')) === 'b69265_wp'){
				echo 'here';
				$wpdb->query( "DROP TABLE IF EXISTS " . $table );
			}
		}
		return;*/

			foreach ($sites as $site){
				switch_to_blog($site->blog_id);

				$prefix = $wpdb->prefix;
				$prefixes[] = $prefix;

				echo '<p>' . $site->domain . ' | ';echo $prefix;$t = null; $u = "NOT";
				foreach ($tables as $key => $table){ //@TODO: this will select unused tables, but there is a chance these to be used outside WP

					$t = $table;

					if(
						(substr($table, 0, strlen($prefix)) === $prefix && ($prefix !== $this->main_site_prefix))
						|| $this->is_main_site_table($table)){ // MAIN SITE PREFIX IS OFTEN wp_
						unset($unused_tables[$key]);
						$u = 'USED';
					}


				}
				echo ' | ' . ((string) substr($t, 0, strlen($prefix)) === $prefix || $prefix === $this->main_site_prefix) . ' || ' . $u .'</p>';echo '<h1>------------------END TABLE LOOP------------------</h1>';

				restore_current_blog();
			}

			return $unused_tables;
	}


	function delete_all_unused_tables(){
		$unused_tables = $this->get_unused_tables();

		foreach ($unused_tables as $table){
			$this->wpdb->query( "DROP TABLE IF EXISTS " . $table );
		}

		return true;
	}

	function delete_unused_tables_after_site_delition($site_id){

		$prefix = $this->wpdb->get_blog_prefix($site_id);


		if($prefix === $this->main_site_prefix) return;
		$unused_tables = $this->get_tables_with_prefix($prefix);

		foreach ($unused_tables as $table){
			$this->wpdb->query( "DROP TABLE IF EXISTS " . $table );
		}
	}

	private function is_main_site_table($table){
		$after_prefix_char = substr($table, strlen($this->main_site_prefix), 1);
		return !is_numeric($after_prefix_char);
	}
}