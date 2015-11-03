<?php

class TTKHelper {

    static function getUUID() {
        return sprintf( '%04x%04x_%04x_%04x_%04x_%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}

class Source {
    public $original = '';
    public $machine = '';
    public $manual = '';

    public function __construct($original = '', $machine = '', $manual = '')
    {
        $this->original = $original;
        $this->machine = $machine;
        $this->manual = $manual;
    }
}

class Sentence {
    //TODO: 
    public $id;
    public $source;
    public $type = "sentence";
    public $displayed = "original";

    public function __construct( $sourceText = '' )
    {
        $this->id = "sentence_".TTKHelper::getUUID();
        $this->source = new Source( $sourceText );
    }
}

class Paragraph {
    public $id;
    public $type = "paragraph";
    public $sentences = array();

    public function __construct( $sourceText = '' )
    {
        $this->id = "paragraph_".TTKHelper::getUUID();

        $sentences = preg_split( '/(?<!\w\.\w.)(?<![A-Z][a-z]\.)(?<=\.|\?)\s/', $sourceText );
        //print_r($sentences);
        foreach( $sentences as $sentence ) {
            //print_r($sentence."\n");
            $this->addSentence( $sentence );
        }
    }

    public function addSentence( $sourceText )
    {
        $sentence = new Sentence( $sourceText );
        array_push( $this->sentences, $sentence );
    }
}

class Article {
    public $id;
    public $type = "article";
    public $selectedSentence;
    public $paragraphs = array();

    public function __construct( $sourceText = '' )
    {
        $this->id = "article_".TTKHelper::getUUID();

        $paragraphs = preg_split( '/(?>\r\n|\n|\r)/', $sourceText );
        //print_r( $paragraphs );
        foreach( $paragraphs as $paragraph ) {
            //print_r($paragraph."\n");
            $this->addParagraph( $paragraph );
        }
    }

    public function addParagraph( $sourceText )
    {
        $paragraph = new Paragraph( $sourceText );
        array_push( $this->paragraphs, $paragraph );
    }
}

header('Content-Type: application/json');

$sourceText = "Hello World. Hello Hell. Hello War.
Hello World. Hello Hell. Hello War.
Hello World. Hello Hell. Hello War.";

$sourceText = ( empty( $_POST['article'] ) ) ? $sourceText : urldecode( $_POST['article'] );

$article = new Article($sourceText);

print_r( json_encode( $article ) );

?>
