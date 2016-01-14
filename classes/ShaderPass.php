<?php

namespace Iekadou\Webapp;

class ShaderPass extends BaseModel
{
    protected $table = 'shaderpass';
    protected $fields = array('userid', 'program_id', 'shader_id', 'enabled', 'needs_swap', 'rank');

    public function get_program_id() {
        return $this->program_id;
    }

    public function set_program_id($program_id) {
        $this->program_id = $program_id;
        return $this;
    }

    public function get_program() {
        $Program = new Program();
        $Program = $Program->get($this->get_program_id());
        return $Program;
    }

    public function get_shader_id() {
        return $this->shader_id;
    }

    public function set_shader_id($shader_id) {
        $this->shader_id = $shader_id;
        return $this;
    }

    public function get_shader() {
        $Shader = new Shader();
        $Shader = $Shader->get($this->get_shader_id());
        if (!$Shader) {
            $Shader = new Shader();
            $Shader = $Shader->set_userid($this->get_userid())->set_name('shader - '.$this->get_program()->get_name())->create();
            $this->set_shader_id($Shader->get_id())->save();
        }
        return $Shader;
    }

    public function get_enabled() {
        return $this->enabled;
    }

    public function set_enabled($enabled) {
        $this->enabled = $enabled;
        return $this;
    }

    public function get_needs_swap() {
        return $this->needs_swap;
    }

    public function set_needs_swap($needs_swap) {
        $this->needs_swap = $needs_swap;
        return $this;
    }

    public function get_rank() {
        return $this->rank;
    }

    public function set_rank($rank) {
        $this->rank = $rank;
        return $this;
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
        if (isset($POST['program_id'])) {
            $this->set_program_id($POST['program_id']);
        }
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }
}
