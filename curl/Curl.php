<?php

class CURL{

    
    function httpRequest($url, $post = "", $retry = false, $retryNumber = 0, $headers = array()) {
    sleep(3);
    global $settings;
    try {
        $ch = curl_init();
        //Change the user agent below suitably
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
        
        curl_setopt($ch, CURLOPT_URL, ($url));
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $settings['cookiefile']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $settings['cookiefile']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($post))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 
        if (!empty($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (!$response) {
            throw new Exception("Error getting data from server: " . curl_error($ch));
        }
        curl_close($ch);
    }
    catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
        if (!$retry && $retryNumber < 3) {
            echo "Retrying \n";
            httpRequest($url, $post, true, $retryNumber++);
        } else {
            echo "Could not perform action after 3 attempts. Skipping now...\n";
            return null;
        }
    }

    return $response;
}
}

?>