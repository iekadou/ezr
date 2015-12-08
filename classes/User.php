<?php

namespace Iekadou\Webapp;

class User extends BaseModel
{
    protected $table = 'user';
    protected $fields = array('username', 'email', 'password', 'activated', 'activation_key');
    
    public function get_username()
    {
        return $this->username;
    }
    
    public function set_username($username)
    {
        $username = $this->db_connection->real_escape_string(htmlentities($username, ENT_QUOTES));
        if ($this->id) { $idcheck = ' and id != '.$this->id.''; } else { $idcheck = ""; }
        $query_check_user_name = $this->db_connection->query("SELECT * FROM ".$this->table." WHERE username = '" . $username . "' ".$idcheck.";");
        if ($query_check_user_name->num_rows > 0) {
            $this->errors[] = "username";
            throw new ValidationError($this->errors);
        } else {
            $this->username = $username;
        }
        return $this;
    }

    public function get_activation_key() {
        return $this->activation_key;
    }

    public function activate() {
        $this->set_activated(true);
        return $this;
    }

    public function deactivate() {
        $this->set_activated(false);
        return $this;
    }

    public function is_activated() {
        return $this->activated;
    }

    public function set_activated($activated) {
        $this->activated = $activated;
        return $this;
    }

    public function get_email()
    {    
        return $this->email;
    }

    public function set_email($email)
    {    
        $email = $this->db_connection->real_escape_string(htmlentities($email, ENT_QUOTES));
        if ($this->id) { $idcheck = ' and id != '.$this->id.''; } else { $idcheck = ""; }
        $query_check_user_user_email = $this->db_connection->query("SELECT * FROM ".$this->table." WHERE email = '" . $email . "' ".$idcheck.";");
        if ($query_check_user_user_email->num_rows > 0) {
            $this->errors[] = "email";
            throw new ValidationError($this->errors);
        } else {
            $this->email = $email;
        }
        return $this;
    }

    public function set_password($password)
    {
        $password = $this->db_connection->real_escape_string(htmlentities($password, ENT_QUOTES));
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function register_new_user($username, $email, $password)
    {
        $this->set_email($email);
        $this->set_username($username);
        $this->set_password($password);

        if (!isset($this->errors) || empty($this->errors)) {
            if ($this->create()) {
                $this->id = $this->db_connection->get_insert_id();
                if ($this->send_activation_email()) {
                    return $this;
                } else {
                    $this->errors[] = 'activation';
                }
            }
        }
        throw new ValidationError($this->errors);
    }

    private function generate_activation_key() {
        $activation_key = '';
        while ($activation_key == '') {
            for($length = 0; $length < 20; $length++) {
                $chr_cat = rand(0,1);
                switch ($chr_cat) {
                    case 0:
                        $char = chr(rand(50,57));
                        break;
                    default:
                        $char = chr(rand(97,122));
                }
                $activation_key .= $char;
            }
            $query_activation_key = $this->db_connection->query("SELECT * FROM user WHERE activation_key = '" . $activation_key . "';");
            if ($query_activation_key->num_rows > 0) {
                $activation_key = '';
            }
        }
        $this->activation_key = $activation_key;
    }
    
    public function send_activation_email() {
        $this->generate_activation_key();
        $this->save();
        $subject = Translation::translate('Your account at {{ SITE_NAME }}', array('{{ SITE_NAME }}' => SITE_NAME));
        $content = Translation::translate("Hey {{ username }},
you can activate your account by clicking the following link:
https://{{ DOMAIN }}{{ activate_url }}

Have fun on {{ SITE_NAME }}", array('{{ username }}' => $this->get_username(), '{{ activation_key }}' => $this->get_activation_key(), '{{ SITE_NAME }}' => SITE_NAME, '{{ DOMAIN }}' => DOMAIN, '{{ activate_url }}' => UrlsPy::get_url('activate', array($this->get_activation_key(),))));
        $header = 'From: '.NO_REPLY_EMAIL;
        if (mail($this->get_email(), $subject, $content, $header)) {
            return $this;
        } else {
            throw new ValidationError(array());
        }
    }
    
    public function send_new_password() {

        $new_password = '';
        for($length = 0; $length < 20; $length++) {
            $chr_cat = rand(0,1);
            switch ($chr_cat) {
                case 0:
                    $char = chr(rand(50,57));
                    break;
                default:
                    $char = chr(rand(97,122));
            }
            $new_password .= $char;
        }
        
        $subject = Translation::translate('Your new password at {{ SITE_NAME }}', array('{{ SITE_NAME }}' => SITE_NAME));
        $content = Translation::translate('Hey {{ username }},
your new password is: {{ new_password }}

Have fun on {{ SITE_NAME }}', array('{{ SITE_NAME }}' => SITE_NAME, '{{ username }}' => $this->get_username(), '{{ new_password }}' => $new_password));
        $header = 'From: '.NO_REPLY_EMAIL;
        if (mail($this->get_email(), $subject, $content, $header)) {
            $this->set_password($new_password);
            $this->save();
            return $this;
        } else {
            throw new ValidationError(array());
        }
    }

    public function interpret_user($POST, $FILES) {
        $username = (isset($POST['username']) ? htmlspecialchars($POST['username']) : false);
        if (!preg_match("/^[a-zA-Z0-9 ]{3,50}$/", $username)) {
            $this->errors[] = 'username';
        }
        $this->set_username($username);
        $email = (isset($POST['email']) ? htmlspecialchars($POST['email']) : false);
        if (!preg_match('/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_\-]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $email)) {
            $this->errors[] = 'email';
        }
        $this->set_email($email);
        if (isset($_POST['password']) && $_POST['password'] != '') {
            $password = (isset($POST['password']) ? htmlspecialchars($POST['password']) : false);
            if (!preg_match("/^[a-zA-Z0-9 ]{3,50}$/", $password)) {
                $this->errors[] = 'password';
            }
            $password_repeat = (isset($POST['password_repeat']) ? htmlspecialchars($POST['password_repeat']) : false);
            if (!preg_match("/^[a-zA-Z0-9 ]{3,50}$/", $password_repeat)) {
                $this->errors[] = 'password_repeat';
            }
            if ($password != $password_repeat) {
                $this->errors[] = 'password';
                $this->errors[] = 'password_repeat';
            }
            $this->set_password($password);
        }
        if (!empty($this->errors)) {
            throw new ValidationError($this->errors);
        }
        return $this;
    }
}
