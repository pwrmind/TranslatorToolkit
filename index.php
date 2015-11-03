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

$sourceText = "Hello World. Hello Hell. Hello War.
Hello World. Hello Hell. Hello War.
Hello World. Hello Hell. Hello War.";

$sourceText = ( empty( $_POST['article'] ) ) ? $sourceText : urldecode( $_POST['article'] );

$article = new Article($sourceText);
$articleJSON = json_encode( $article );

if ($_POST["article"]){
    header('Content-Type: application/json');
    print_r( json_encode( $article ) );
}
?>

<?php
if (!$_POST["article"]):       
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link type="text/css" rel="stylesheet" href="css/main.css">
        
        <script src="js/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular-route.js"></script>
        <script src="http://yastatic.net/jquery/hotkeys/1.0.0/jquery.hotkeys.min.js"></script>        
        <script src="js/main.js"></script>
    </head>
    <body>
        <div id="workspace" class="workspace" ng-app="translatorToolkitApp">
            <div class="workspace-panel" ng-controller="workspacePanelCtrl">
                <div class="button button-send" ng-click="sendForPreparing()">Send</div>
            </div>
            <div class="article" ng-controller="articleCtrl">
                <p class="paragraph" ng-repeat="paragraph in article.paragraphs" ng-attr-id="{{ paragraph.id }}">
                    <span class="sentence" ng-repeat="sensence in paragraph.sentences" ng-attr-id="{{ sensence.id }}" ng-click="focus(sensence)">
                        {{ sensence.source[sensence.displayed] }}
                    </span>
                </p>
            </div>
            <div id="editor" ng-controller="editorCtrl">
                <div class="editor-panel">
                  <div class="block-back-and-forth">
                      <div id="buttonBack" class="button button-back">Back (Ctrl+Up)</div>
                      <div id="buttonForth" class="button button-forth">Forth (Ctrl+Down)</div>
                  </div>
                  <div id="buttonApply" class="button button-apply-translation" ng-click="applyManualTranslation()">Применить</div>
                      <div id="buttonCancel" class="button button-cancel-translation" ng-click="cancelManualTranslation()">Отменить</div>
                      <div id="buttonClose" class="button button-close">Х</div>
                </div>
                <textarea class="editor-manual-translation" ng-model="article.selectedSentence.source['manual']">{{article.selectedSentence.source["manual"]}}</textarea>
                <div class="editor-machine-translation" ng-model="article.selectedSentence.source['manual']">{{article.selectedSentence.source["machine"]}}</div>
            </div>
        </div>
        <script>
            angular.module('translatorToolkitApp', ['ngRoute'])
            .run(function ($rootScope, $http) {
                $rootScope.article = <?php echo $articleJSON; ?>;
            })
            .controller('articleCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
                $scope.focus = function (sensence) {
                    $('#' + sensence.id).after($('#editor'));
                    $rootScope.article.selectedSentence = sensence;
                };
            } ])
            .controller('editorCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
                $scope.applyManualTranslation = function () {
                    $rootScope.article.selectedSentence.displayed = "manual";
                };
                $scope.cancelManualTranslation = function () {
                    $rootScope.article.selectedSentence.displayed = "original";
                    //$rootScope.article.selectedSentence.source['manual'] = $rootScope.article.selectedSentence.source['machine'];
                };
            } ])
            .controller('workspacePanelCtrl', ['$scope', '$rootScope', '$route', function ($scope, $route, $rootScope) {
                $scope.sendForPreparing = function () {
                    //debugger;
                    var sourceText =  encodeURI("Hello world. Hello hell.");
                    $.post("index.php",
                    {
                        article: sourceText
                    },
                    function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        $('#workspace').append($('#editor'));
                        $rootScope.article = data;
                        $route.reload();
                    });
                    //$rootScope.article.selectedSentence.displayed = "manual";
                };
            } ]);
        </script>
    </body>
</html>
<?php
endif;          
?>
