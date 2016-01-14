<?php

namespace Iekadou\Webapp;

class Snippet extends BaseModel
{
    public static $SnippetTypes = array('init'=>1, 'render'=>2, 'vertex'=>3, 'fragment'=>4, 'uniforms'=>5);
    public static $SnippetTypeNames = array(1=>'init', 2=>'render',3=>'vertex', 4=>'fragment', 5=>'uniforms');

    protected $table = 'snippet';
    protected $fields = array('userid', 'name', 'type', 'code');

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
        if (isset(Snippet::$SnippetTypeNames[$this->type])) {
            return Snippet::$SnippetTypeNames[$this->type];
        }
        return "-";
    }

    public function set_type($type) {
        if (isset(Snippet::$SnippetTypes[$type])) {
            $this->type = Snippet::$SnippetTypes[$type];
        } else {
            $this->errors[] = 'type';
        }
        return $this;
    }

    public function get_code() {
        return $this->code;
    }

    public function set_code($code) {
        $code = $this->db_connection->real_escape_string($code);
        $this->code = $code;
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
        $this->set_code($POST['code']);
        $this->set_name($POST['name']);
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }

}
