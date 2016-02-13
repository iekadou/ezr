(function(Webapp, $, undefined) {
    Webapp.resolveMethodName = function(string) {
        // splitting the strings at dots to be able to resolve method names in namespaces
        // setting Pointer to window to be in the root of namespaces
        try {
            var data = string.split('.');
            var pointer = window;
            $.each(data, function(key, value) {
                // for every namespace or method of the callback set the pointer on it
                pointer = pointer[value];
            });
            // if the last part of the namespace points to a function, set it as the callback
            if (typeof pointer === 'function') {
                return pointer;
            }
        } catch (e) {
            // given callback method is invalid
        }
        return function() {};
    };

    Webapp.toastr_opts = { closeButton: true,
        closeHtml: '<button><span class="fa fa-times"></span></button>',
        positionClass: 'toast-top-left',
        hideDuration: 300 };

    Webapp.register_api_forms = function() {
        $('.api-form').off('submit').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            $form.find('.btn-primary').addClass('disabled');
            var data = new FormData(this);
            data.append('_method', $form.attr('method'));
            Webapp.api_post($form.attr('action'), data, function(){}, $form);
            $form.find('.btn-primary').removeClass('disabled');
        }).find('input').off('keypress').on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                $(this).submit();
            }
        });
        $('.api-form .btn-submit').off('click').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.api-form').submit();
        });
    };
    Webapp.api_post = function(url, data, callback, $form) {
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data, successCode, jqXHR) {
                if (data.url !== undefined) {
                    location.href = data.url;
                } else {
                    for (var field in data) {
                        if (field == 'error_msgs') {
                            for (var i = 0; i < data[field].length; i++) {
                                toastr.error(data[field][i].message, data[field][i].title, Webapp.toastr_opts);
                            }
                        } else if (field == 'success_msgs') {
                            for (var i = 0; i < data[field].length; i++) {
                                toastr.success(data[field][i].message, data[field][i].title, Webapp.toastr_opts);
                            }
                        } else if (field == 'info_msgs') {
                            for (var i = 0; i < data[field].length; i++) {
                                toastr.in(data[field][i].message, data[field][i].title, Webapp.toastr_opts);
                            }
                        } else {
                            if (data[field] == 'error') {
                                if ($form !== undefined) {
                                    $form.find('[name="' + field + '"]').closest('.form-group').addClass('has-error');
                                }
                            }
                        }
                    }
                }
                if (callback) {
                    callback(data, successCode, jqXHR);
                }
                if ($form !== undefined) {
                    Webapp.resolveMethodName($form.attr('successCallback'))(data, successCode, jqXHR);
                }
            },
            error: function (jqXHR, errorCode, errorThrown) {
                if ($form !== undefined) {
                    Webapp.resolveMethodName($form.attr('errorCallback'))(jqXHR, errorCode, errorThrown);
                }
                toastr.error(Webapp.error_label, Webapp.error_title, Webapp.toastr_opts);
            },
            complete: function (jqXHR, statusCode) {
                if ($form !== undefined) {
                    Webapp.resolveMethodName($form.attr('completeCallback'))();
                    $form.find('.btn-primary').removeClass('disabled');
                }
            }
        });
    };
    Webapp.closeNavbar = function() {
        $('#inner_navbar.dropdown.open .dropdown-toggle').dropdown('toggle');
    };

}(window.Webapp = window.Webapp || {}, jQuery));


$(document).lareAlways(function() {
    Webapp.register_api_forms();
    if ($.support.lare) {
        $('a').not('[data-non-lare]').on('click', function(event) {
            $(document).lare.click(event, {timeout: 20000});
        });
    }
    Webapp.closeNavbar();
});