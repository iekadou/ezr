<?php

namespace Iekadou\Webapp;

class Texture extends BaseModel
{

    protected $table = 'texture';
    protected $fields = array('userid', 'program_id', 'name', 'img');

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

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $name = $this->db_connection->real_escape_string($name);
        $this->name = $name;
        return $this;
    }

    public function get_img() {
        return $this->img;
    }

    public function set_img($img) {
        $this->img = $img;
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

    public function delete($id=null)
    {
        if ($id) {
            $id = $this->db_connection->real_escape_string(htmlentities($id, ENT_QUOTES));
        } else {
            $id = $this->id;
        }
        if ($id) {
            $texture = new Texture();
            $texture = $texture->get($id);
            unlink(PATH.$texture->get_img());
            $delete_query = $this->db_connection->query("DELETE FROM ".$this->table." WHERE id = '" . $id . "';");
            if ($delete_query) {
                return true;
            }
        }
        return false;
    }

    public function interpret_request($POST, $FILES) {

        $image = null;
        if (isset($FILES['image']) && isset($FILES['image']['error']) && $FILES['image']['error'] == 0) {
            $image = $FILES['image'];
            $uploaddir = PATH."uploads/";
            $ext = pathinfo(basename($image['name']), PATHINFO_EXTENSION);
            $uniqueId = uniqid();
            $uploadfile = $uploaddir.$uniqueId.basename($image['name']);
            $allowed_extensions = Files::get_allowed_extensions();
            if (in_array($image['type'], $allowed_extensions[strtolower($ext)]['mime'])) {
                if (move_uploaded_file($image['tmp_name'], $uploadfile)) {
                    $image = "uploads/".$uniqueId.basename($image['name']);
                    fixOrientation($uploadfile);
                } else {
                    $image = null;
                }
            } else {
                $image = null;
            }
            $this->set_img($image);
        }
        $this->set_name($POST['name']);

        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }

}
