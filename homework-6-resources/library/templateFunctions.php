<?php

function renderLayoutWithContentFile($contentFile, $variables = array()) {
    $contentFileFullPath = TEMPLATES_PATH . '/' . $contentFile;

    // making sure passed in variables are in scope of the template  
    // each key in the $variables array will become a variable  
    if (count($variables) > 0) {
        foreach ($variables as $key => $value) {
            if (strlen($key) > 0) {
                ${$key} = $value;
            }
        }
    }

    require TEMPLATES_PATH . '/header.php';

    if (file_exists($contentFileFullPath)) {
        require $contentFileFullPath;
    } else {
        /*
          If the file isn't found the error can be handled in lots of ways.
          In this case we will just include an error template.
         */
        require TEMPLATES_PATH . '/error_public.php';
    }

    require TEMPLATES_PATH . '/footer.php';
}