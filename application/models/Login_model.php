<?php
class Login_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getLogin($login)
    {
        return $this->db->get_where('user', array('email' => $login));
    }
    public function forgotPassword($email)
    {
        $this->db->select('email');
        $this->db->from('user');
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function sendpassword($data)
    {
        $email = $data['email'];
        $query1 = $this->db->query("SELECT *  from user where email = '" . $email . "' ");
        $row = $query1->result_array();
        if ($query1->num_rows() > 0) {
            $passwordplain = "";
            $passwordplain = rand(999999999, 9999999999);
            $newpass['password'] = $passwordplain;// md5($passwordplain);
            $this->db->where('email', $email);
            $this->db->update('user', $newpass);
            $mail_message = 'Dear ' . $row[0]['first_name'] . ',' . "\r\n";
            $mail_message .= 'Thanks for contacting regarding to forgot password,<br> Your <b>Password</b> is <b>' . $passwordplain . '</b>' . "\r\n";
            $mail_message .= '<br>Please Update your password.';
            $mail_message .= '<br>Thanks & Regards';
            $mail_message .= '<br>Your company name';
            date_default_timezone_set('Etc/UTC');
            $this->load->library("phpmailer_library");
            $mail = $this->phpmailer_library->load();
            $mail->isSMTP();
            $mail->SMTPSecure = "tls";
            $mail->Debugoutput = 'html';
            $mail->Host = "smtp.live.com";
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = "wjmarcolin@hotmail.com";
            $mail->Password = "Wilson010695";
            $mail->setFrom('wjmarcolin@hotmail.com', 'admin');
            $mail->IsHTML(true);
            $mail->addAddress($email);
            $mail->Subject = 'OTP from company';
            $mail->Body = $mail_message;
            $mail->AltBody = $mail_message;
            if (!$mail->send()) {
                $this->session->set_flashdata('msg', 'Failed to send password, please try again!');
            } else {
                $this->session->set_flashdata('msg', 'Password sent to your email!');
            }
            redirect(base_url() . 'home/login', 'refresh');
        } else {
            $this->session->set_flashdata('msg', 'Email not found try again!');
            redirect(base_url() . 'home/Login', 'refresh');
        }
    }
}