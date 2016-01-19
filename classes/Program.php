<?php

namespace Iekadou\Webapp;

class Program extends BaseModel
{

    protected $table = 'program';
    protected $fields = array('userid', 'name', 'init_id', 'render_id', 'material_id', 'object_type');

    public static $ObjectTypes = array('cube'=>1, 'sphere'=>2, 'torus'=>3);
    public static $ObjectTypeNames = array(1=>'cube', 2=>'sphere',3=>'torus');

    public function get_object_type() {
        return $this->object_type;
    }

    public function get_object_type_display() {
        if (isset(Program::$ObjectTypeNames[$this->object_type])) {
            return Program::$ObjectTypeNames[$this->object_type];
        }
        return "-";
    }

    public function set_object_type($object_type) {
        if (isset(Program::$ObjectTypes[$object_type])) {
            $this->object_type = Program::$ObjectTypes[$object_type];
        } else {
            $this->errors[] = 'object_type';
        }
        return $this;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $name = $this->db_connection->real_escape_string($name);
        $this->name = $name;
        return $this;
    }

    public function get_material_id() {
        return $this->material_id;
    }

    public function set_material_id($material_id) {
        $this->material_id = $material_id;
        return $this;
    }

    public function get_material() {
        $Shader = new Shader();
        $Shader = $Shader->get($this->get_material_id());
        if (!$Shader) {
            $Shader = new Shader();
            $Shader = $Shader->set_userid($this->get_userid())->set_name('material - '.$this->get_name())->create();
            $this->set_material_id($Shader->get_id())->save();
        }
        return $Shader;
    }

    public function get_init_id() {
        return $this->init_id;
    }

    public function set_init_id($init_id) {
        $this->init_id = $init_id;
        return $this;
    }

    public function get_init() {
        $Init = new Snippet();
        $Init = $Init->get($this->get_init_id());
        if (!$Init) {
            $Init = new Snippet();
            $Init = $Init->set_userid($this->get_userid())->set_type(Snippet::$SnippetTypes['init'])->set_name('Init - '.$this->get_name())->create();
            $this->set_init_id($Init->get_id())->save();
        }
        return $Init;
    }

    public function get_render_id() {
        return $this->render_id;
    }

    public function set_render_id($render_id) {
        $this->render_id = $render_id;
        return $this;
    }

    public function get_render() {
        $Render = new Snippet();
        $Render = $Render->get($this->get_render_id());
        if (!$Render) {
            $Render = new Snippet();
            $Render = $Render->set_userid($this->get_userid())->set_type(Snippet::$SnippetTypes['render'])->set_name('render - '.$this->get_name())->create();
            $this->set_render_id($Render->get_id())->save();
        }
        return $Render;
    }

    public function get_userid() {
        return $this->userid;
    }

    public function set_userid($userid) {
        $userid = $this->db_connection->real_escape_string($userid);
        $this->userid = $userid;
        return $this;
    }

    public function interpret_request($POST, $FILES) {
        $this->set_name($POST['name']);
        $this->set_object_type($POST['object_type']);
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }

    public function get_shader_passes() {
        $ShaderPass = new ShaderPass();
        return $ShaderPass->filter_by(array(array('program_id', '=', $this->id)));
    }

}
