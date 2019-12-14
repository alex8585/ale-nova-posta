<?php


class Ale_Nova_Posta_Public {

	private $ale_nova_posta;
	private $version;

	public function __construct( $ale_nova_posta, $version ) {
		$this->view = new Ale_Nova_Posta_View();
		$this->ale_nova_posta = $ale_nova_posta;
		$this->version = $version;
		$this->flash = new Ale_Nova_Posta_Flash();
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		
		wp_enqueue_style( $this->ale_nova_posta, plugin_dir_url( __FILE__ ) . 'css/ale-nova-posta-public.css', array(), $this->version, 'all' );
		
	    //wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
	
		
	wp_enqueue_script( 'jquery-ui.js', plugin_dir_url( __FILE__ ) . 
		'js/jquery-ui.js', array( 'jquery' ), $this->version, false );


	wp_register_script( $this->ale_nova_posta, plugin_dir_url( __FILE__ ) . 
		'js/ale-nova-posta-public.js', array( 'jquery' ), $this->version, false );

	wp_enqueue_script($this->ale_nova_posta);
	wp_localize_script( $this->ale_nova_posta, 'wp_obj', array( 
		'wp_ajax_url' => admin_url("admin-ajax.php"),
	) );
		
	}
	
	
	public function plugin_init() {
		
		add_action( 'wp_ajax_search_city', [$this,'ajax_search_city'] ); 
		add_action( 'wp_ajax_nopriv_search_city', [$this,'ajax_search_city'] );

		add_action( 'wp_ajax_get_warehouses', [$this,'ajax_get_warehouses'] ); 
		add_action( 'wp_ajax_nopriv_get_warehouses', [$this,'ajax_get_warehouses'] );
		
	}

	public function init() {
		
		add_filter( 'cron_schedules', [$this, 'np_intervals']); 
		add_action( 'update_cities_from_np_hook', [$this,'update_cities_from_np'] );
		
		//wp_clear_scheduled_hook( 'update_cities_from_np_hook' );
		if( !wp_next_scheduled('update_cities_from_np_hook') ) {
			//daily
			wp_schedule_event( time(), 'daily', 'update_cities_from_np_hook' );
		}

	}

	public function wp() {
		
	}

	public function np_intervals( $scheduling ) {
		$scheduling['five_min'] = [
			'interval' => 60*5 , 
			'display' => 'Every 5 minutes'
		];
		return $scheduling;
	}

	public function update_cities_from_np() {
		$cities = $this->get_cities_from_np();
		$this->save_cities_to_db($cities);
	}


    public function save_cities_to_db($cities) {

		if(!$cities) return 0;

		global $wpdb;

		$sql = "INSERT INTO ". ALE_NOVA_POSTA_CITIES_TABLE. "(
			CityID,
			Description,
			DescriptionRu,
			Ref,
			SettlementTypeDescription,
			SettlementTypeDescriptionRu
		)
		VALUES ";

		foreach($cities as $city) {
			 
			$elem = new stdClass();
			$elem->CityID = esc_sql($city->CityID);
			$elem->Description = esc_sql($city->Description);
			$elem->DescriptionRu = esc_sql($city->DescriptionRu);
			$elem->Ref = esc_sql($city->Ref);
			$elem->SettlementTypeDescription = isset($city->SettlementTypeDescription) ? esc_sql($city->SettlementTypeDescription) : '';
			$elem->SettlementTypeDescriptionRu = isset($city->SettlementTypeDescriptionRu) ? esc_sql($city->SettlementTypeDescriptionRu) : '';

			$sql .= " (  
				'{$elem->CityID}', 
				'{$elem->Description}', 
				'{$elem->DescriptionRu}',
				'{$elem->Ref}' , 
				'{$elem->SettlementTypeDescription}', 
				'{$elem->SettlementTypeDescriptionRu}' 
			),";


		}

		$sql = substr_replace($sql,';',-1);

		$wpdb->query("TRUNCATE TABLE ".ALE_NOVA_POSTA_CITIES_TABLE);
		$result = $wpdb->query($sql);
		return $result;
		
	}

	public function get_cities_from_np() {
		require ALE_NOVA_POSTA_PATH . 'vendor/autoload.php';

		$all_options = get_option(ALE_NP);
		$apiKey = $all_options['api_key'];

		$client = new GuzzleHttp\Client();
		$res = $client->request('POST', 'https://api.novaposhta.ua/v2.0/json/', [
			'json' => [
				'apiKey' => $apiKey,
        		'modelName' => 'Address',
        		'calledMethod' => 'getCities',
    		],
		]);
		
		
		if($res->getStatusCode() == "200") {
			$jsonRes = (json_decode($res->getBody()->getContents()));
			if($jsonRes->success == '1') {
				$data = $jsonRes->data;
				//print_r($data);
				return $data;
			}
		}
	}



	public function ajax_get_warehouses() {
		$search_str  = isset($_POST['search_str']) ? $_POST['search_str'] : '';

		if(!$search_str) {
			wp_send_json( [] );die;
		}

		global $wpdb;
		$sql = "SELECT `Ref` FROM ".ALE_NOVA_POSTA_CITIES_TABLE. 
		" WHERE `DescriptionRu`  = '{$search_str}' ";

		$cityRef = $wpdb->get_var($sql);

		if(!$cityRef) {
			wp_send_json( $search_str );die;
		}

		$warehouses = $this->get_warehouses_np($cityRef);
		if(!$warehouses) {
			wp_send_json([] );die;
		}
		
		$warehousesArr = [];
		foreach($warehouses as $warehouse) {
			if(isset($warehouse->DescriptionRu)) {
				$warehousesArr[] = $warehouse->DescriptionRu;
			}
		}

		$return = array(
			'warehouses'   => $warehousesArr,
		);
		
		wp_send_json( $return ); 
		die;
	}

	public function get_warehouses_np($cityRef) {
		require ALE_NOVA_POSTA_PATH . 'vendor/autoload.php';

		$all_options = get_option(ALE_NP);
		$apiKey = $all_options['api_key'];

		$client = new GuzzleHttp\Client();
		$res = $client->request('POST', 'https://api.novaposhta.ua/v2.0/json/', [
			'json' => [
				"apiKey"=> $apiKey,
        		'modelName' => 'Address',
				"calledMethod"=> "getWarehouses",
				"methodProperties"=> [
					"CityRef"=> $cityRef
				],
    		],
		]);
		

		if($res->getStatusCode() == "200") {
			$jsonRes = (json_decode($res->getBody()->getContents()));
			if($jsonRes->success == '1') {
				$data = $jsonRes->data;
				return $data;
			}
		}
		return [];
	}


	public function ajax_search_city() {
		$search_str  = isset($_POST['search_str']) ? $_POST['search_str'] : '';

		if(!$search_str) {
			wp_send_json( [] );
			die;
		}

		global $wpdb;

		$sql = "SELECT `DescriptionRu` FROM ".ALE_NOVA_POSTA_CITIES_TABLE. 
			" WHERE `Description`  LIKE '{$search_str}%' OR `DescriptionRu`  LIKE '{$search_str}%'";

		$cities = $wpdb->get_col($sql);

		$return = array(
			'cities'   => $cities,
		);
		
		wp_send_json( $return );
		die;
	}


	

	




	



	

	


	



}
