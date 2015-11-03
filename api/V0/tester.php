<?php
    echo 'Ho ho ho';
    $url = "";

    echo $url;

    if( $ch = curl_init() ) {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        echo $output;
        curl_close($ch);  
    } else {
        $output =  "{ translation: 'error' }";
    }

    echo $output;

    $responseDecoded = json_decode($output);
        
    echo $responseDecoded->translation;

?>

