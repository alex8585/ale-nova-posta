<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/admin
 * @author     Your Name <email@example.com>
 */
class Ale_Nova_Posta_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ale_nova_posta    The ID of this plugin.
	 */
	private $ale_nova_posta;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $ale_nova_posta       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $ale_nova_posta, $version ) {

		$this->ale_nova_posta = $ale_nova_posta;
		$this->version = $version;
		$this->setings_page = ALE_NP;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ale_Nova_Posta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ale_Nova_Posta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ale_nova_posta, plugin_dir_url( __FILE__ ) . 'css/ale-nova-posta-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ale_Nova_Posta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ale_Nova_Posta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( $this->ale_nova_posta, plugin_dir_url( __FILE__ ) . 'js/ale-nova-posta-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function plugin_init() {
		
		add_action('admin_menu', [$this,'ale_nova_posta_menu']);
		add_action( 'admin_init', [$this, 'ale_nova_posta_settings'] );
		add_action( 'update_option_ale-nova-posta', [$this,'run_after_change'], 10, 3 );
	}



	public function run_after_change( $old_value, $value, $option ){
		do_action('update_cities_from_np_hook');
	}

	public function wp() {
		
	}

	public function admin_init() {
		
	}

	
	


	public function ale_nova_posta_menu() {
		add_options_page( 
			__('Новая почта API', ALE_NP),
			__('Новая почта API', ALE_NP), 
			'manage_options', 
			$this->setings_page, 
		[$this, 'ale_nova_posta_menu_page']); 
	
	}
	public function ale_nova_posta_menu_page(){
	
		?><div class="wrap">
		
			<h2> <?php _e('Настройки Новая почта API', ALE_NP) ?></h2>
			<form method="post" enctype="multipart/form-data" action="options.php">
				<?php 
				settings_fields($this->setings_page); 
				do_settings_sections($this->setings_page);
				?>
				<p class="submit">  
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
				</p>
			</form>
			<?php 
			
			//$all_options1 = get_option($this->setings_page);
			//print_r($all_options1);
			?>
		</div><?php
	}
	
	public function ale_nova_posta_settings() {
	
		register_setting( $this->setings_page, $this->setings_page, [$this,'validate_settings'] ); 
	 
		
		add_settings_section(  $this->setings_page.'_section', '', '', $this->setings_page );
	 
		
		$params = array(
			'type'      => 'text', 
			'id'        => 'api_key',
			'desc'      => __('Api ключ новой почты', ALE_NP), 
			'label_for' => 'api_key'
		);
		add_settings_field( 
			'api_key', 
			__('API Key', ALE_NP),  
			[$this,'display_field'], 
			$this->setings_page, 
			$this->setings_page.'_section', 
			$params );
	}

	function validate_settings($input) {
		foreach($input as $k => $v) {
			$valid_input[$k] = trim($v);
		}
		//print_r($valid_input);die;
		return $valid_input;
	}





	function display_field($args) {
		extract( $args );
	 
	 
		$o = get_option( $this->setings_page );
		
		switch ( $type ) {  
			case 'text':  
				$o[$id] = esc_attr( stripslashes($o[$id]) );
				echo "<input class='regular-text' type='text' id='$id' name='" . $this->setings_page . "[$id]' value='$o[$id]' />";  
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
			break;
			case 'textarea':  
				$o[$id] = esc_attr( stripslashes($o[$id]) );
				echo "<textarea class='code large-text' cols='30' rows='3' type='text' id='$id' name='" . $this->setings_page . "[$id]'>$o[$id]</textarea>";  
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
			break;
			case 'checkbox':
				$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';  
				echo "<label><input type='checkbox' id='$id' name='" . $this->setings_page . "[$id]' $checked /> ";  
				echo ($desc != '') ? $desc : "";
				echo "</label>";  
			break;
			case 'select':
				echo "<select id='$id' name='" . $this->setings_page . "[$id]'>";
				foreach($vals as $v=>$l){
					$selected = ($o[$id] == $v) ? "selected='selected'" : '';  
					echo "<option value='$v' $selected>$l</option>";
				}
				echo ($desc != '') ? $desc : "";
				echo "</select>";  
			break;
			case 'radio':
				echo "<fieldset>";
				foreach($vals as $v=>$l){
					$checked = ($o[$id] == $v) ? "checked='checked'" : '';  
					echo "<label><input type='radio' name='" . $this->setings_page . "[$id]' value='$v' $checked />$l</label><br />";
				}
				echo "</fieldset>";  
			break; 
		}
	}
}

