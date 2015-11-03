<?php
    include 'translators\yandex\Translator.php';//use translators\yandex\Translator;
    include 'translators\yandex\Exception.php';//use translators\yandex\Exception;

    //header('Content-Type: application/json; charset=utf-8');
    //$article = "";
    //$paragraph = "";
    //$sentence = urldecode( $_POST["sentence"] );
    //$translatedSentence = translate( $sentence );

    function translate( $sentence ) {
        $translatedSentence = "";//$sentence;

        $translatedSentence = translateInFrengly($sentence);

        return $translatedSentence;
    }

    function translateInOwnTranslator( $sentence ) {
        $translatedSentence = "";
        return $translatedSentence;
    }

    function translateInMyMemory( $sentence ) {
        $translatedSentence = "";
        return $translatedSentence;
    }

    function translateInFrengly( $sentence ) {
        $translatedSentence = "";
        $url = "";
        $url = "http://frengly.com?"
        ."src=en&"
        ."dest=ru&"
        ."text=".$sentence."&"
        ."email=babanoff@bk.ru&"
        ."password=fre-\$w0rdF1sh&"
        ."outformat=json";

        $output =  "{ translation: 'get requesr' }";
        //echo $output;
        if( $ch = curl_init() ) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch); 
            echo $output;
            curl_close($ch);  
        } else {
            $output =  "{ translation: 'error' }";
        }
            
        
        //$response = file_get_contents($url);
        
        $responseDecoded = json_decode($output);
        
        $translatedSentence = $responseDecoded->translation;
        return $translatedSentence;
    }

    function translateInYandex( $sentence ) {
        $apiKey = '<paste your API key here>';
        $translatedSentence = "";

        try {
          $translator = new Translator($apiKey);
          $translatedSentence = $translator->translate($sentence, 'en-ru');

          //echo $translation; // Привет мир

          //echo $translation->getSource(); // Hello world;

          //echo $translation->getSourceLanguage(); // en
          //echo $translation->getResultLanguage(); // ru
        } catch (Exception $e) {
          // handle exception
        }

        return $translatedSentence;
    }

    function translateInGoogle( $sentence ) {
        $translatedSentence = "";
        $apiKey = '<paste your API key here>';
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($sentence) . '&source=en&target=ru';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);                 
        $responseDecoded = json_decode($response, true);
        curl_close($handle);

        $translatedSentence = $responseDecoded['data']['translations'][0]['translatedText'];

        return $translatedSentence;
    }

    function translateInMicrosoft( $sentence ) {
        $translatedSentence = "";
        return $translatedSentence;
    }

    //echo json_encode($translatedSentence);
?>
