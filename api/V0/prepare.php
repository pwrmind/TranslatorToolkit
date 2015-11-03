<?php
    include 'translator.php';

    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    header('Content-Type: application/json; charset=utf-8');
    $article = urldecode( $_POST["article"] );

    $articleJSON = json_decode( "{}" );
    $articleJSON->id = "article_".gen_uuid();
    $articleJSON->type = "article";
    $articleJSON->selectedSentence = json_decode( "{}" );
    $articleJSON->paragraphs = json_decode( "[]" );

    $paragraphs = preg_split( '/(?>\r\n|\n|\r)/', $article );

    foreach( $paragraphs as $paragraph ) {
        $paragraphJSON = json_decode( "{}" );
        $paragraphJSON->id = "paragraph_".gen_uuid();
        $paragraphJSON->type = "paragraph";
        $paragraphJSON->sentences = json_decode( "[]" );

        $sentences = preg_split( '/(?<!\w\.\w.)(?<![A-Z][a-z]\.)(?<=\.|\?)\s/', $paragraph );

        foreach( $sentences as $sentence ) {
            $sentenceJSON = json_decode( "{}" );
            $sentenceJSON->id = "sentence_".gen_uuid();
            $sentenceJSON->type = "sentence";            
            $sentenceJSON->displayed = "original";
            $sentenceJSON->source = json_decode( "{}" );
            $sentenceJSON->source->original = $sentence;

            $translatedSentence = translate($sentence);

            $sentenceJSON->source->machine = $translatedSentence;
            $sentenceJSON->source->manual = $translatedSentence;

            array_push( $paragraphJSON->sentences, $sentenceJSON );
        }

        array_push( $articleJSON->paragraphs, $paragraphJSON );
    }

    echo json_encode($articleJSON);
?>
