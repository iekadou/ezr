<?php

namespace Iekadou\Webapp;

class Shader extends BaseModel
{

    protected $table = 'shader';
    protected $fields = array('userid', 'name', 'vertex_id', 'fragment_id');

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $name = $this->db_connection->real_escape_string($name);
        $this->name = $name;
        return $this;
    }

    public function get_vertex_id() {
        return $this->vertex_id;
    }

    public function set_vertex_id($vertex_id) {
        $this->vertex_id = $vertex_id;
        return $this;
    }

    public function get_vertex() {
        $Vertex = new Snippet();
        $Vertex = $Vertex->get($this->get_vertex_id());
        if (!$Vertex) {
            $Vertex = new Snippet();
            $Vertex = $Vertex->set_userid($this->get_userid())->set_type(Snippet::$SnippetTypes['vertex'])->set_name('vertex - '.$this->get_name())->set_code('// Predefined uniforms:
// uniform mat4 modelMatrix;
// uniform mat4 modelViewMatrix;
// uniform mat4 projectionMatrix;
// uniform mat4 viewMatrix;
// uniform mat3 normalMatrix;
// uniform vec3 cameraPosition;
// uniform vec2 resolution;
// uniform float time;

// Available attributes
// attribute vec3 position;
// attribute vec3 normal;
// attribute vec2 uv;
// attribute vec2 uv2;

varying vec3 world_position;

void main() {
    gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
    world_position = position + 0.5;
}
')->create();
            $this->set_vertex_id($Vertex->get_id())->save();
        }
        return $Vertex;
    }

    public function get_fragment_id() {
        return $this->fragment_id;
    }

    public function set_fragment_id($fragment_id) {
        $this->fragment_id = $fragment_id;
        return $this;
    }

    public function get_fragment() {
        $Fragment = new Snippet();
        $Fragment = $Fragment->get($this->get_fragment_id());
        if (!$Fragment) {
            $Fragment = new Snippet();
            $Fragment = $Fragment->set_userid($this->get_userid())->set_type(Snippet::$SnippetTypes['fragment'])->set_name('fragment - '.$this->get_name())->set_code('// Predefined uniforms:
// uniform mat4 viewMatrix;
// uniform vec3 cameraPosition;

varying vec3 world_position;

void main(void) {
    gl_FragColor = vec4( world_position.x, world_position.y, world_position.z, 1.0 );
}
')->create();
            $this->set_fragment_id($Fragment->get_id())->save();
        }
        return $Fragment;
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
        $this->get_fragment()->set_code($POST['fragment_code'])->save();
        $this->get_vertex()->set_code($POST['vertex_code'])->save();
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }

}
