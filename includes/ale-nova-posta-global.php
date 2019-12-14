<?php

define("ALE_NP", 'ale-nova-posta');
define("ALE_NOVA_POSTA_CITIES_TABLE", 'ale_nova_posta_cities');



if(function_exists('print_filters_for') ) {
    function print_filters_for( $hook = '' ) {
        global $wp_filter;
        if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
            return;
    
        print '<pre>';
        print_r( $wp_filter[$hook] );
        print '</pre>';
    }
}

