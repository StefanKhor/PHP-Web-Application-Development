<?php

function generateJavaScriptAlert($message) {
    echo<<<HTML
    <script>
        alert('{$message}');
    </script>
    HTML;
}

function setCustomMessage($elementName,$message){
    echo <<<HTML
    <script>
        document.getElementById('{$elementName}').innerHTML = '{$message}';
    </script>
    HTML;
}

?>