<?php

namespace Iekadou\Webapp;

class Shader extends BaseModel
{
    public static $ShaderTypes = array('vertex'=>1, 'fragment'=>2);
    public static $ShaderTypeNames = array(1=>'vertex', 2=>'fragment');

    protected $table = 'shader';
    protected $fields = array('userid', 'name', 'type', 'program');


    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $name = $this->db_connection->real_escape_string($name);
        $this->name = $name;
        return $this;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_type_display() {
        if (isset(Shader::$ShaderTypeNames[$this->type])) {
            return Shader::$ShaderTypeNames[$this->type];
        }
        return "-";
    }

    public function set_type($type) {
        if (isset(Shader::$ShaderTypes[$type])) {
            $this->type = Shader::$ShaderTypes[$type];
        } else {
            $this->errors[] = 'type';
        }
        return $this;
    }

    public function get_program() {
        return $this->program;
    }

    public function set_program($program) {
        $program = $this->db_connection->real_escape_string($program);
        $this->program = $program;
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
        $this->set_program($POST['program']);
        $this->set_type($POST['type']);
        $this->set_name($POST['name']);
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }

}
