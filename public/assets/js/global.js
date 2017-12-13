$(document).ready(function() {
    $(document).on('click', '#trigger-logout', function(e) {
        e.preventDefault();

        var postData = {}; postData[$('[name=csrf-name]').attr('content')] = $('[name=csrf-value]').attr('content');
        console.log(postData);
        $.post($(this).attr('href'), postData, function(data) {
            location.reload();
        });
    });

    $(document).on('click', '[data-toggle*=modal]', function(e) {
        e.preventDefault();

        triggerModal($(this).attr('href'));
    });

    window.loadedTabs = [];
    $(document).on('click', '.tab-ajax', function(e) {
        e.preventDefault();
        var url = $(this).attr('data-url');
        var params = callFunctionIfExists($(this).attr('data-params'));

        if ($.inArray(this.hash, window.loadedTabs) === -1 && typeof url !== 'undefined') {
            var pane = $(this), href = this.hash;

            $.ajax({
                url: url,
                cache: true,
                data: params
            }).done(function(response) {
                $(href).append(response);
            });

            window.loadedTabs.push(href);
            pane.tab('show');
        } else {
            $(this).tab('show');
        }
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});

var getBrowserLocation = function(callback) {
    navigator.geolocation.getCurrentPosition(function(position) {
        window.browserCenter = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        };

        typeof callback === 'function' && callback(position);
    }, function() {
        return false;
    });
};

var findNearbyLocation = function() {
    if (typeof window.browserCenter !== 'undefined') {
        return window.browserCenter;
    }

    if (typeof window.userCenter !== 'undefined') {
        return window.userCenter;
    }

    return false;
}

var fetchNearby = function(center, radius, callback) {
    $.ajax({
        url: 'locations/nearby',
        data: {
            latitude: center.lat(),
            longitude: center.lng(),
            radius: radius,
            type: 'json'
        }
    }).done(function(response) {
        callback(response);
    });
}

var triggerModal = function(href, data) {
    var modalId = new Date().getUTCMilliseconds() * new Date().getUTCMilliseconds();
    var modal = '<div class="modal fade" id="modal_' + modalId + '" tabindex="-1" role="dialog" aria-hidden="true"></div>';
    $('body').append(modal);

    $.ajax({
        url: href,
        data: data
    }).done(function(response) {
        $(response).appendTo($('#modal_' + modalId));

        $('#modal_' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
        }).modal('show');
    });
}

var callFunctionIfExists = function(fnName) {
    fn = window[fnName];
    fnExists = typeof fn === 'function';
    if (fnExists) {
        return fn();
    }

    return false;
}

var debounce = function(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };

        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};