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
            $.ajax({
                method: "POST",
                url: $form.attr('action'),
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                form: $form,
                success: function (data, successCode, jqXHR) {
                    $form.find('.form-group').removeClass('has-error');
                    if (data.additional_url) {
                        location.href = data.additional_url;
                    }
                    if (data.url !== undefined) {
                        location.href = data.url;
                    } else {
                        for (var field in data) {
                            if (field == 'error_msgs') {
                                for (var i = 0; i < data[field].length; i++) {
                                    toastr.error(data[field][i].message, data[field][i].title, Webapp.toastr_opts);
                                }
                            } else if (field == 'info_msgs') {
                                for (var msg in data[field]) {
                                    toastr.info(msg[0], msg[1], Webapp.toastr_opts);
                                }
                            } else if (field == 'success_msgs') {
                                for (var msg in data[field]) {
                                    toastr.success(msg[0], msg[1], Webapp.toastr_opts);
                                }
                            } else {
                                if (data[field] == 'error') {
                                    $form.find('[name="' + field + '"]').closest('.form-group').addClass('has-error');
                                }
                            }
                        }
                    }
                    Webapp.resolveMethodName($form.attr('successCallback'))();
                },
                error: function (jqXHR, errorCode, errorThrown) {
                    Webapp.resolveMethodName($form.attr('errorCallback'))();
                    toastr.error(Webapp.error_label, Webapp.error_title, Webapp.toastr_opts);
                },
                complete: function (jqXHR, statusCode) {
                    Webapp.resolveMethodName($form.attr('completeCallback'))();
                    $form.find('.btn-primary').removeClass('disabled');
                }
            });
            $form.find('.btn-primary').removeClass('disabled');
        }).find('input').off('keypress').on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                $(this).submit();
            }
        });
        $('.api-form .btn-submit').off('click').on('click', function (e) {
            $(this).closest('.api-form').submit();
        });
    };
    $('.btn-code-register').off('click').on('click', function(e) {
        $('#code_register').removeClass('hide');
        $('#code_no_register').addClass('hide');
        $('#code_register form').prepend($('#code_no_register form').children('.form-group'));
        $(this).addClass('btn-submit').removeClass('btn-code-register');
        Webapp.register_api_forms();
    });
    $('.btn-delete').off('click').on('click', function(e) {
        e.preventDefault();
        var $modal = $('#delete-modal');
        $modal.find('input[name="obj_id"]').val($(this).attr("data-id"));
        $modal.find('input[name="obj_class"]').val($(this).attr("data-class"));
        $modal.find('input[name="obj_extra"]').val($(this).attr("data-extra"));
        $modal.modal('show');
    });
    $('.btn-qr-modal').off('click').on('click', function(e) {
        e.preventDefault();
        var $modal = $('#qr-modal');
        $modal.find('.qr-code').attr('src', '/qr/'+$(this).attr('data-string')+'/');
        $modal.find('.code-link').attr('href', '/code/'+$(this).attr('data-string')+'/');
        $modal.find('.code-link').html('https://'+Webapp.domain+'/code/'+$(this).attr('data-string')+'/');
        $modal.modal('show');
    });
    $('.btn-contact-info').off('click').on('click', function(e) {
        e.preventDefault();
        var $modal = $('#contact-info-modal');
        Webapp.initCodeDetailTable($modal.find('.code-detail-container'), $(this));
        $modal.modal('show');
    });
    Webapp.initCodeDetailTable = function($table, $dataWrapper) {
        var fields = ['first_name', 'phone', 'email', 'image', 'company', 'home_street', 'home_number', 'home_postal_code', 'home_city', 'home_country', 'home_additional', 'work_street', 'work_number', 'work_postal_code', 'work_city', 'work_country', 'work_additional', 'mobile_phone', 'home_phone'];
        for (var i = 0; i < fields.length; i++ ) {
            var field = fields[i];
            if ($dataWrapper.attr('data-'+field) && $dataWrapper.attr('data-'+field) != '' && $dataWrapper.attr('data-'+field) != undefined) {
                if (field == 'image') {
                    $table.find('.code-'+field).html('<a href="/'+$dataWrapper.attr('data-'+field)+'" target="_blank"><img src="/'+$dataWrapper.attr('data-'+field)+'" style="max-height: 150px;"></a>');
                } else if(field == 'first_name') {
                    $table.find('.code-'+field).html($dataWrapper.attr('data-first_name') + ' ' + $dataWrapper.attr('data-last_name'));
                } else {
                    $table.find('.code-'+field).html($dataWrapper.attr('data-'+field));
                }
                $table.find('.code-wrapper-'+field).removeClass('hide');
            } else {
                $table.find('.code-wrapper-'+field).addClass('hide');
            }
        }
    };
    Webapp.closeNavbar = function() {
        $('#inner_navbar.dropdown.open .dropdown-toggle').dropdown('toggle');
    };
}(window.Webapp = window.Webapp || {}, jQuery));


$(document).lareAlways(function() {
    $('#add-shader-pass').off('click').on('click', function() {
        var data = new FormData();
        data.append('program_id', $(this).attr('data-program-id'));
        data.append('_method', "POST");
        $.ajax({
            method: "POST",
            url: '/api/account/shaderpass/',
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data, successCode, jqXHR) {
                alert("POST!");
            }
        });
    });
    $('.preview-snippet').off('click').on('click', function(e) {
        e.preventDefault();
        var $snippet = $(this).closest('.ace-wrapper');
        snippets[$snippet.find('.snippet').attr('id').split("snippet_")[1]] = editors[$snippet.find('.snippet').attr('id')].getSession().getValue();
        init();
    });
    $('.save-snippet').off('click').on('click', function(e) {
        e.preventDefault();
        var $snippet = $(this).closest('.ace-wrapper');
        var data = new FormData();
        data.append('name', $snippet.find('[name="name"]').val());
        data.append('id', $(this).attr('data-id'));
        data.append('code', editors[$snippet.find('.snippet').attr('id')].getSession().getValue());
        data.append('_method', "PUT");
        Webapp.api_post('/api/account/snippet/', data);
    });
    $('.preview-shader').off('click').on('click', function(e) {
        e.preventDefault();
        var $shader = $(this).closest('.ace-wrapper');
        snippets[$shader.find('.vertex').attr('id').split("snippet_")[1]] = editors[$shader.find('.vertex').attr('id')].getSession().getValue();
        snippets[$shader.find('.fragment').attr('id').split("snippet_")[1]] = editors[$shader.find('.fragment').attr('id')].getSession().getValue();
        init();
    });
    $('.save-shader').off('click').on('click', function(e) {
        e.preventDefault();
        var $shader = $(this).closest('.ace-wrapper');
        var data = new FormData();
        data.append('name', $shader.find('[name="name"]').val());
        data.append('id', $(this).attr('data-id'));
        data.append('vertex_code', editors[$shader.find('.vertex').attr('id')].getSession().getValue());
        data.append('fragment_code', editors[$shader.find('.fragment').attr('id')].getSession().getValue());
        data.append('_method', "PUT");
        Webapp.api_post('/api/account/shader/', data);
    });
    $('.save-program').off('click').on('click', function(e) {
        e.preventDefault();
        var $program = $(this).closest('.ace-wrapper');
        var data = new FormData();
        data.append('name', $program.find('[name="name"]').val());
        data.append('object_type', $program.find('[name="object_type"]').val());
        data.append('id', $(this).attr('data-id'));
        data.append('_method', "PUT");
        Webapp.api_post('/api/account/program/', data);
    });
    $('.preview-program').off('click').on('click', function(e) {
        e.preventDefault();
        init();
    });
    $('#object_type').off('change').on('change', function(e) {
        e.preventDefault();
        init();
    });
    Webapp.api_post = function(url, data) {
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
                        }
                    }
                }
                init();
            }
        });
    };

    $('.shaderpasses').sortable();
    $('.ace_editor').each(function() {
        var $id = $(this).attr('id');
        editors[$id] = ace.edit($(this).attr('id'));
        editors[$id].setTheme("ace/theme/monokai");
        editors[$id].commands.addCommand({
            name: 'preview',
            bindKey: {win: "Ctrl-D", mac: "Cmd-D"},
            exec: function(editor) {
                $(editor.container).closest('.ace-wrapper').find('.preview').click();
            },
            readOnly: true
        });
        editors[$id].commands.addCommand({
            name: 'save',
            bindKey: {win: "Ctrl-S", mac: "Cmd-S"},
            exec: function(editor) {
                $(editor.container).closest('.ace-wrapper').find('.save').click();
            },
            readOnly: true
        });
        editors[$(this).attr('id')].getSession().setMode("ace/mode/"+$(this).attr('data-mode'));
        $(".resizable_" + $id).resizable({
            minHeight: 300,
            resize: function( event, ui ) {
                editors[$id].resize();
            }
        });
    });

    Webapp.register_api_forms();
    if ($.support.lare) {
        $('a').not('[data-non-lare]').on('click', function(event) {
            $(document).lare.click(event, {timeout: 20000});
        });
    }
    Webapp.closeNavbar();
    $(document).keydown(function(event) {
        // CMD / CTRL + S
        if((event.ctrlKey || event.metaKey) && event.which == 83) {
            // Save Function
            event.preventDefault();
            $(event.target).closest('.ace-wrapper').find('.save').click();
            return false;
        }
        // CMD / CTRL + S
        if((event.ctrlKey || event.metaKey) && event.which == 68) {
            // Save Function
            event.preventDefault();
            $(event.target).closest('.ace-wrapper').find('.preview').click();
            return false;
        }
    });
});