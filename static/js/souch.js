(function(Webapp, $, undefined) {

    Webapp.register_ace_editors = function () {
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
    };
    Webapp.register_program_btns = function() {
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
            Webapp.api_post('/api/account/snippet/', data, function(data, successCode, jqXHR) { init(); });
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
            Webapp.api_post('/api/account/shader/', data, function(data, successCode, jqXHR) { init(); });
        });
        $('.save-program').off('click').on('click', function(e) {
            e.preventDefault();
            var $program = $(this).closest('.ace-wrapper');
            var data = new FormData();
            data.append('name', $program.find('[name="name"]').val());
            data.append('object_type', $program.find('[name="object_type"]').val());
            data.append('id', $(this).attr('data-id'));
            data.append('_method', "PUT");
            Webapp.api_post('/api/account/program/', data, function(data, successCode, jqXHR) { init(); });
        });
        $('.preview-program').off('click').on('click', function(e) {
            e.preventDefault();
            init();
        });
        $('#add-shader-pass').off('click').on('click', function(e) {
            e.preventDefault();
            var data = new FormData();
            data.append('program_id', $(this).attr('data-program-id'));
            data.append('_method', "POST");
            Webapp.api_post('/api/account/shaderpass/', data, function(data, successCode, jqXHR) {
                var $new_shader = $(data.rendered_html);
                $('#add-shader-pass').before($new_shader);
                shader_passes.push({'id': data.id, 'vertex_id': data.vertex_id,'fragment_id': data.fragment_id});
                snippets[data.vertex_id] = '';
                snippets[data.fragment_id] = '';
                Webapp.register_ace_editors();
                Webapp.register_program_btns();
                $new_shader.find('.preview-shader').click();
                init();
            });
        });
        $('.delete-shader-pass').off('click').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var $modal = $('#delete-modal');
            $modal.modal('show');
            $modal.find('.confirm-delete').off('click').on('click', function(e) {
                e.preventDefault();
                var data = new FormData();
                data.append('id', id);
                data.append('_method', "DELETE");
                Webapp.api_post('/api/account/shaderpass/', data, function(data, successCode, jqXHR) {
                    $('#shaderpass_'+id).remove();
                    for (var i = 0; i < shader_passes.length; i++) {
                        if (shader_passes[i].id == id) {
                            shader_passes.splice(i, 1);
                            break;
                        }
                    }
                    init();
                });
                $modal.modal('hide');
            });
        });
        $('.delete-program').off('click').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var $modal = $('#delete-modal');
            $modal.modal('show');
            $modal.find('.confirm-delete').off('click').on('click', function(e) {
                e.preventDefault();
                var data = new FormData();
                data.append('id', id);
                data.append('_method', "DELETE");
                Webapp.api_post('/api/account/program/', data, function(data, successCode, jqXHR) {
                    $('#program_'+id).remove();
                });
                $modal.modal('hide');
            });
        });
        $('#object_type').off('change').on('change', function(e) {
            e.preventDefault();
            init();
        });
    };

}(window.Webapp = window.Webapp || {}, jQuery));

$(document).lareAlways(function() {
    if ($.support.lare) {
        $('a').not('[data-non-lare]').on('click', function(event) {
            $(document).lare.click(event, {timeout: 20000});
        });
    }
    Webapp.closeNavbar();
    Webapp.register_program_btns();
    //$('.shaderpasses').sortable();
    Webapp.register_ace_editors();
    Webapp.register_api_forms();
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