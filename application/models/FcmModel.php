<?php if ( ! defined('BASEPATH')) exit('Noinsert_data direct script access allowed');


class FcmModel extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    

    public function sendNotifictaion($title, $message, $tokens,$click_action)
    {
    
        $authKey = "AAAAXridHas:APA91bGgaW-0fRFh0nnUxQVaQiMMAcd2sOg4JhHoP4m5PK7m4NVgf410zIYgmjUDmtUGdU0qgYNdWEd2p5pXGOy6jffN44dkrq_fCXWWN6uFTfJ4W0onKNAbiUlvKvx_5tI1VgOl0c94";
        $url = 'https://fcm.googleapis.com/fcm/send';
    
        $fields = array(
            'registration_ids' =>
           $tokens,
           'notification' =>array(
                "title" => $title,
                "body" => $message,
            ),
            'data' => array(
                "click_action" => $click_action,
            )
        );
        $fields = json_encode($fields);
    
        $headers = array(
            'Authorization: key=' . $authKey,
            'Content-Type: application/json'
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        // echo $result;
        // die;
        curl_close($ch);
        return true;
    }


	
}
?>