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
        $('.save-renderpass').off('click').on('click', function(e) {
            e.preventDefault();
            var $renderPass = $(this).closest('.ace-wrapper');
            var data = new FormData();
            data.append('id', $(this).attr('data-id'));
            data.append('name', $renderPass.find('[name="name"]').val());
            data.append('texture_name', $renderPass.find('[name="texture_name"]').val());
            data.append('vertex_code', editors[$renderPass.find('.vertex').attr('id')].getSession().getValue());
            data.append('fragment_code', editors[$renderPass.find('.fragment').attr('id')].getSession().getValue());
            data.append('_method', "PUT");
            Webapp.api_post('/api/account/renderpass/', data, function(data, successCode, jqXHR) { init(); });
        });
        $('.preview-renderpass').off('click').on('click', function(e) {
            e.preventDefault();
            var $renderPass = $(this).closest('.ace-wrapper');
            snippets[$renderPass.find('.vertex').attr('id').split("snippet_")[1]] = editors[$renderPass.find('.vertex').attr('id')].getSession().getValue();
            snippets[$renderPass.find('.fragment').attr('id').split("snippet_")[1]] = editors[$renderPass.find('.fragment').attr('id')].getSession().getValue();

            for (var i=0 ; i < renderPasses.length; i++) {
                if (renderPasses[i].id !== undefined && renderPasses[i].id == $(this).attr('data-id')) {
                    renderPasses[i].material = new THREE.ShaderMaterial({
                        uniforms: {},
                        vertexShader: snippets[$renderPass.find('.vertex').attr('id').split("snippet_")[1]],/* vertex_shader */
                        fragmentShader: snippets[$renderPass.find('.fragment').attr('id').split("snippet_")[1]]/* fragment_shader */
                    });
                    renderPasses[i].texture_name = $renderPass.find('[name="texture_name"]').val();
                }
            }

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
                shaderPasses.push({'id': data.id, 'vertex_id': data.vertex_id,'fragment_id': data.fragment_id});
                snippets[data.vertex_id] = data.vertex_shader;
                snippets[data.fragment_id] = data.fragment_shader;
                Webapp.register_ace_editors();
                editors['snippet_'+data.vertex_id].getSession().setValue(snippets[+data.vertex_id]);
                editors['snippet_'+data.fragment_id].getSession().setValue(snippets[data.fragment_id]);
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
                    for (var i = 0; i < shaderPasses.length; i++) {
                        if (shaderPasses[i].id == id) {
                            shaderPasses.splice(i, 1);
                            break;
                        }
                    }
                    init();
                });
                $modal.modal('hide');
            });
        });
        $('#add-render-pass').off('click').on('click', function(e) {
            e.preventDefault();
            var data = new FormData();
            data.append('program_id', $(this).attr('data-program-id'));
            data.append('_method', "POST");
            Webapp.api_post('/api/account/renderpass/', data, function(data, successCode, jqXHR) {
                var $new_shader = $(data.rendered_html);
                $('#add-render-pass').before($new_shader);
                snippets[data.vertex_id] = data.vertex_shader;
                snippets[data.fragment_id] = data.fragment_shader;
                renderPasses.push({'scene': new THREE.Scene(),
                    'id': data.id,
                    'material': new THREE.ShaderMaterial({
                        uniforms: {},
                        vertexShader: snippets[data.vertex_id],/* vertex_shader */
                        fragmentShader: snippets[data.fragment_id]/* fragment_shader */
                    }),
                    'render_target': new THREE.WebGLRenderTarget( window.innerWidth/2, window.innerHeight, { minFilter: THREE.LinearFilter, magFilter: THREE.NearestFilter, format: THREE.RGBFormat } ),
                    'texture_name': data.texture_name
                });
                Webapp.register_ace_editors();
                editors['snippet_'+data.vertex_id].getSession().setValue(snippets[+data.vertex_id]);
                editors['snippet_'+data.fragment_id].getSession().setValue(snippets[data.fragment_id]);
                Webapp.register_program_btns();
                $new_shader.find('.preview-shader').click();
                init();
            });
        });
        $('.delete-render-pass').off('click').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var $modal = $('#delete-modal');
            $modal.modal('show');
            $modal.find('.confirm-delete').off('click').on('click', function(e) {
                e.preventDefault();
                var data = new FormData();
                data.append('id', id);
                data.append('_method', "DELETE");
                Webapp.api_post('/api/account/renderpass/', data, function(data, successCode, jqXHR) {
                    $('#renderpass_'+id).remove();
                    for (var i = 0; i < renderPasses.length; i++) {
                        if (renderPasses[i].id == id) {
                            renderPasses.splice(i, 1);
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
    try {
        initOnce();

        for (var key in snippets) {
            if (snippets.hasOwnProperty(key)) {
                $('#snippet_'+key).val(snippets[key]);
                editors['snippet_'+key].getSession().setValue(snippets[key]);
            }
        }

        init();
        animate();
    } catch (Exception) {

    }
});