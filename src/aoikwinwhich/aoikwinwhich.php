<?php

#/ define a string |endswith| function
function endsWith($str, $sub) {
    return (substr($str, strlen($str) - strlen($sub)) == $sub);
}

function find_executable($prog) {
    #/ 8f1kRCu
    $env_var_PATHEXT = getenv('PATHEXT');
    ## can be False

    #/ 6qhHTHF
    #/ split into a list of extensions
    $ext_s = ($env_var_PATHEXT === False)
        ? []
        : explode(PATH_SEPARATOR, $env_var_PATHEXT);

    #/ 2pGJrMW
    #/ strip
    $ext_s = array_map(function($x) {
        return trim($x);
    }, $ext_s);

    #/ 2gqeHHl
    #/ remove empty
    $ext_s = array_filter($ext_s, function($x) {
        return $x !== '';
    });

    #/ 2zdGM8W
    #/ convert to lowercase
    $ext_s = array_map(function($x) {
        return strtolower($x);
    }, $ext_s);

    #/ 2fT8aRB
    #/ uniquify
    $ext_s = array_unique($ext_s);

    #/ 4ysaQVN
    $env_var_PATH = getenv('PATH');
    ## can be False

    #/ 6mPI0lg
    $dir_path_s = ($env_var_PATH === False)
        ? []
        : explode(PATH_SEPARATOR, $env_var_PATH);

    #/ 5rT49zI
    #/ insert empty dir path to the beginning
    ##
    ## Empty dir handles the case that |prog| is a path, either relative or
    ##  absolute. See code 7rO7NIN.
    array_unshift($dir_path_s, '');

    #/ 2klTv20
    #/ uniquify
    $dir_path_s = array_unique($dir_path_s);

    #/ 6bFwhbv
    $exe_path_s = Array();

    foreach ($dir_path_s as $dir_path) {
        #/ 7rO7NIN
        #/ synthesize a path with the dir and prog
        if ($dir_path === '') {
            $path = $prog;
        }
        else {
            $path = implode(DIRECTORY_SEPARATOR, array($dir_path, $prog));
        }

        #/ 6kZa5cq
        ## assume the path has extension, check if it is an executable
        $path_has_ext = array_filter($ext_s, function($ext) use ($path){
            return endsWith($path, $ext);
        }) !== array();

        if ($path_has_ext && is_file($path)) {
            $exe_path_s[] = $path;
        }

        #/ 2sJhhEV
        ## assume the path has no extension
        foreach ($ext_s as $ext) {
            #/ 6k9X6GP
            #/ synthesize a new path with the path and the executable extension
            $path_plus_ext = $path . $ext;

            #/ 6kabzQg
            #/ check if it is an executable
            if (is_file($path_plus_ext)) {
                $exe_path_s[] = $path_plus_ext;
            }
        }
    }

    #/
    return $exe_path_s;
}

function println($txt) {
    print($txt);
    print("\n");
}

function main() {
    #/ 9mlJlKg
    global $argv;

    $arg_s = array_slice($argv, 1);

    if (count($arg_s) != 1) {
        #/ 7rOUXFo
        #/ print program usage
        println('Usage: aoikwinwhich PROG');
        println('');
        println('#/ PROG can be either name or path');
        println('aoikwinwhich notepad.exe');
        println('aoikwinwhich C:\Windows\notepad.exe');
        println('');
        println('#/ PROG can be either absolute or relative');
        println('aoikwinwhich C:\Windows\notepad.exe');
        println('aoikwinwhich Windows\notepad.exe');
        println('');
        println('#/ PROG can be either with or without extension');
        println('aoikwinwhich notepad.exe');
        println('aoikwinwhich notepad');
        println('aoikwinwhich C:\Windows\notepad.exe');
        println('aoikwinwhich C:\Windows\notepad');

        #/ 3nqHnP7
        return;
    }

    #/ 9m5B08H
    #/ get name or path of a program from cmd arg
    $prog = $arg_s[0];

    #/ 8ulvPXM
    #/ find executables
    $path_s = find_executable($prog);

    #/ 5fWrcaF
    #/ has found none, exit
    if (empty($path_s)) {
        #/ 3uswpx0
        return;
    }

    #/ 9xPCWuS
    #/ has found some, output
    $txt = implode("\n", $path_s);

    println($txt);

    #/ 4s1yY1b
    return;
}

#/
if (!debug_backtrace())
{
    main();
}
?>
