import AddressAutocomplete from 'google-address-autocomplete';

$(document).ready(function () {
    // initAutocomplete();
    let placeSearch, autocomplete;

    const options = {
        componentRestrictions: {country: "gr"}
    };

    if ($('#autocomplete-main-address').length) {
        new AddressAutocomplete('#autocomplete-main-address', options, (results) => {
            const addressObject = results;
            $('#checkout_step1_address').val(addressObject.streetName + ' ' + addressObject.streetNumber);
            $('#checkout_step1_zip').val(addressObject.zipCode);
            $('#checkout_step1_city').val(addressObject.cityName);
            $('#checkout_step1_district').val(addressObject.cityName);
        });
    }

    new AddressAutocomplete('#autocomplete-ship-address', options, (results) => {
        const addressShipObject = results;
        // autocomplete.addListener('place_changed', fillInAddress);
        $('#checkout_step1_shipAddress').val(addressShipObject.streetName + ' ' + addressShipObject.streetNumber);
        $('#checkout_step1_shipZip').val(addressShipObject.zipCode);
        $('#checkout_step1_shipCity').val(addressShipObject.cityName);
        $('#checkout_step1_shipDistrict').val(addressShipObject.cityName);
        console.log(addressShipObject);
    });

    $("#autocomplete-main-address").keypress(function (e) {
        // if (e.which == 13) {
        //     e.preventDefault();
        //     let data = {
        //         'keyword': $(this).val()
        //     };
        // }
    });

//     let componentForm = {
//         // street_number: 'checkout_step1_shipAddress',
//         route: 'checkout_step1_shipAddress',
//         locality: 'checkout_step1_shipCity',
//         // administrative_area_level_1: 'short_name',
//         // country: 'long_name',
//         checkout_step1_shipZip: 'short_name'
//     };
// //
//     function initAutocomplete() {
//         // Create the autocomplete object, restricting the search to geographical
//         // location types.
//         autocomplete = new AddressAutocomplete(
//             /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
//             {types: ['geocode']});
//
//         // When the user selects an address from the dropdown, populate the address
//         // fields in the form.
//         autocomplete.addListener('place_changed', fillInAddress);
//     }
// //
//     function fillInAddress() {
//         // Get the place details from the autocomplete object.
//         let place = autocomplete.getPlace();
//
//         for (let component in componentForm) {
//             document.getElementById(component).value = '';
//             document.getElementById(component).disabled = false;
//         }
//
//         // Get each component of the address from the place details
//         // and fill the corresponding field on the form.
//         for (let i = 0; i < place.address_components.length; i++) {
//             let addressType = place.address_components[i].types[0];
//             if (componentForm[addressType]) {
//                 let val = place.address_components[i][componentForm[addressType]];
//                 document.getElementById(addressType).value = val;
//             }
//         }
//     }
//
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
//     function geolocate() {
//         if (navigator.geolocation) {
//             navigator.geolocation.getCurrentPosition(function (position) {
//                 let geolocation = {
//                     lat: position.coords.latitude,
//                     lng: position.coords.longitude
//                 };
//                 let circle = new google.maps.Circle({
//                     center: geolocation,
//                     radius: position.coords.accuracy
//                 });
//                 autocomplete.setBounds(circle.getBounds());
//             });
//         }
//     }
//
//     $("#autocomplete").focus(function() {
//         geolocate();
//     });

});