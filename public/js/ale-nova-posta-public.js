
jQuery(function($) {

    $( document ).ready(function() {
        
        let warehouses = [];
        let ajax_url = wp_obj.wp_ajax_url;

        let cityInput = $( "#billing_city" );
        let warehouseInput = $("#billing_novaposhta_otdelenie");

        cityInput.on('focus', function() {
            let country = $('#billing_country').val();
            let checkNP = $('#check1').prop("checked");

            if(country == 'UA' && checkNP) {
                $(this).autocomplete( "enable" );
            } else {
                $(this).autocomplete( "disable" );
            }

        });

        if(!$('.np-spinner').length) {
            let spinnerElement = $('<div class="lds-facebook np-spinner"><div></div><div></div><div></div></div>');
            warehouseInput.parent().append(spinnerElement);
        }

        if(!$('.np-spinner-city').length) {
            let spinnerElement = $('<div class="lds-facebook np-spinner-city"><div></div><div></div><div></div></div>');
            cityInput.parent().append(spinnerElement);
        }
        


        if(!$('.np-city-msg').length) {
            let msgElement = document.createElement("div");
            msgElement.classList.add('np-city-msg');
            var msgText = document.createTextNode('Сначала выберите город.');
            msgElement.appendChild(msgText);
            $('#content-check1').prepend(msgElement);
        }

        let msg = $('.np-city-msg');
        let spinner = $('.np-spinner');
        let spinnerCity = $('.np-spinner-city');
        
        warehouseInput.autocomplete({ minLength: 0,source:warehouses});
        cityInput.autocomplete({
            source: function( request, response ) {
                spinnerCity.show();

                let sendData = {
                    'action':'search_city',
                    'search_str': cityInput.val(), 
                }
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data:  sendData, 
                }).done(function(data) {
                    msg.hide();
                    warehouseInput.val('');
                    if(!data.cities.length) {
                        spinnerCity.hide();
                    }
                    
                    response(data.cities );
                    }).fail(function() {
                        console.log('fail');
                });
                
            },
            open: function( event, ui ) {
                
                spinnerCity.hide();
                msg.hide();
            },
            change: function( event, ui ) {
                msg.hide();
            },
            select: function( event, ui ) {
                
                spinner.show();
                let sendData = {
                    'action':'get_warehouses',
                    'search_str': ui.item.value, 
                }
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data:  sendData, 
                    }).done(function(data) {
                        warehouses = data.warehouses;
                        
                        warehouseInput.val('');
                        warehouseInput.autocomplete('option', 'source', warehouses);

                        spinner.hide();
                        warehouseInput.trigger( "click" );
                    }).fail(function() {
                        console.log('fail');
                    });
            }
        });

        warehouseInput.on('click',function(e) {
            if(!cityInput.val()) {
                msg.show();
                cityInput.trigger( "focus" );
                return;
            }

            $(this).trigger( "focus" );
            $(this).autocomplete( "search", "" );
        });
        

       /* np_api();
        $(document.body).on('updated_checkout updated_shipping_method', function (event, xhr, data) {
            np_api();
        });*/
    
    });   
});
