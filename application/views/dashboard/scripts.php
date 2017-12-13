<?php echo $apiScript; ?>
<?php echo $mapsScript; ?>
<?php echo $autocompleteScript; ?>

<script type="text/javascript">
    function getPlace(result, el, callback)
    {
        if (result.name.length > 0) {
            var d = { input: result.name, offset: result.name.length };
            autocomplete.getPlacePredictions(d, function (list, status) {
                if (list == null || list.length == 0) callback(null);

                var placesService = new google.maps.places.PlacesService(el);
                var ref = { 'reference': list[0].reference }

                placesService.getDetails(ref, function (detailsResult, placesServiceStatus) {
                    if (placesServiceStatus == google.maps.GeocoderStatus.OK) {
                        callback(detailsResult);
                    }
                    else {
                        callback(null);
                    }
                });
            });
        }
    }

    function getPlaceById(place, callback)
    {
        if (place.placeId.length > 0) {
            placesService.getDetails({placeId: place.placeId}, function(place, status) {
                if (status === 'OK') {
                    callback(place);
                } else {
                    callback(null);
                }
            });
        }
    }
</script>