<?php

// Usage: sublime_to_emacs.php /path/to/my/src_project /path/to/my/cleaned_project

// This script will copy files from src_project/ to cleaned_project/, and re-indenting the ones you need


// ---------------------------- BEG. SETTINGS ----------------------------------------------
// You need to set the files you want to re-indent, and the ones you want to copy, to your cleaned directory

// Files containing these strings in their names will be re-indented
$toIndent = ['.c', '.h'];
// Examples:
// $toIndent = ['.cpp', '.h'];
// $toIndent = ['main.c', 'george.c']; // "I only want to reindent precise files!"


// If null,  the script will copy EVERY file from source to destination
// If [...], the script will only copy files containing the array strings in their names
$copyOnlyTheseFiles = null;
// Examples:
// $copyOnlyTheseFiles = ['Makefile', '.c', '.h'];
// $copyOnlyTheseFiles = ['.cpp', '.h'];
// $copyOnlyTheseFiles = ['.php'];
// If you dont get this option, just let it at null


// Ps : .svn .git .DS_STORE are NOT copied by default (line 174)

// ---------------------------- END SETTINGS -------------------------------------------------


// nothing to configure from there
$usage = 'Usage: php sublime_to_emacs.php /path/to/my/project /path/to/my/cleaned_project' . PHP_EOL;
$selectionMask = $toIndent;
$preciseCopy = (is_array($copyOnlyTheseFiles) and count($copyOnlyTheseFiles));
$preciseCopyMask = $preciseCopy ? $copyOnlyTheseFiles : null;

if ($argc != 3)
    die($usage);

$baseDirectory  = substr($argv[1], -1) !== '/' ? $argv[1] . '/' : $argv[1];
$finalDirectory = substr($argv[2], -1) !== '/' ? $argv[2] . '/' : $argv[2];


if ($baseDirectory === $finalDirectory) {
    die("L'overwriting n'est pas autorisé et vraiment pas recommandé" . PHP_EOL);
}

$confirm = function ($message) {
    $handle = fopen ("php://stdin","r");
    while ($resp !== 'y' and 'n' !== $resp) {
        echo $message . ' (y/n)' . PHP_EOL . '> ';
        $line = fgets($handle);
        $resp = substr($line, 0, strlen($line) - 1);
    }
    fclose($handle);
    return $resp === 'y';
};

$removeFinalDir = false;
if (file_exists($finalDirectory)) {
    $resp = $confirm(realpath($finalDirectory) . ' already exists. Remove ?');
    if (! $resp) {
        die('Pussy' . PHP_EOL);
    }
    $removeFinalDir = true;
}


echo '---------------------------------------------------------------------------';
echo PHP_EOL . 'Sublime to emacs --->  ' . realpath($baseDirectory) . ' copied TO ' . realpath($finalDirectory) . PHP_EOL;
echo PHP_EOL . 'Files copied -------> ';
if ($preciseCopy) {
    foreach ($preciseCopyMask as $idx => $mask) {
        if ($idx) echo ', ';
        echo $mask;
    } echo PHP_EOL;
} else echo 'Every file' . PHP_EOL;
echo PHP_EOL . 'Files re-indented --> ';
foreach ($selectionMask as $idx => $mask) {
    if ($idx) echo ', ';
    echo $mask;
} echo PHP_EOL . PHP_EOL;

if (! $confirm('GO?')) die ('tchiiiiiiiiiiiiiiip' . PHP_EOL);
if ($removeFinalDir) recursiveRemoveDirectory($finalDirectory);
mkdir($finalDirectory, 0777, true);


main($baseDirectory, $finalDirectory, $selectionMask, $preciseCopy, $preciseCopyMask);

echo "Success" . PHP_EOL;

/**
 * @param string $baseDirectory
 * @param string $finalDirectory
 * @param array $selectionMask
 * @param bool $preciseCopy
 * @param array $preciseCopyMask
 * @throws Exception
 */
function main($baseDirectory, $finalDirectory, $selectionMask, $preciseCopy, $preciseCopyMask)
{
    foreach (buildEmacsFilesArray($baseDirectory, $preciseCopy, $preciseCopyMask) as $file) {
        //$path = $finalDirectory . substr($file, 3);
        $path = $finalDirectory . substr($file, strlen($baseDirectory) + 1);

        mkdir(dirname($path), 0777, true);

        foreach ($selectionMask as $masque) {
            if (strrpos($file, $masque)) {
                file_put_contents($path, toEmacs($file));
                continue 2;
            }
        }

        file_put_contents($path, file_get_contents($file));
    }
}

/**
 * @param string $baseDirectory
 * @param bool $preciseCopy
 * @param array $preciseCopyMask
 * @return array
 */
function buildEmacsFilesArray($baseDirectory, $preciseCopy, $preciseCopyMask)
{
    $files = dirToArray($baseDirectory);

    if (! $preciseCopy)
        return $files;

    $res = [];

    foreach ($files as $file) {
        foreach ($preciseCopyMask as $masque) {
            if (strrpos($file, $masque)) {
                $res[] = $file;
                continue;
            }
        }
    }

    return $res;
}

/**
 * @param string $directory
 * @return array
 */
function dirToArray($directory) {
    $arrayItems    = array();
    $skipByExclude = false;
    $handle = opendir($directory);

    if ($handle) {
        while (false !== ($file = readdir($handle))) {
            preg_match("/(^(([\.]){1,2})$|(\.(svn|git))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
            if (!$skip && !$skipByExclude) {
                if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                    $arrayItems = array_merge($arrayItems, dirToArray($directory. DIRECTORY_SEPARATOR . $file));
                } else {
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $arrayItems[] = $file;
                }
            }
        }
        closedir($handle);
    }
    return $arrayItems;
}

/**
 * @param string|null $arg
 * @return string
 * @throws Exception
 */
function toEmacs($arg = null)
{
    if (! $arg)
        throw new \Exception('PAS DE ARG');

    $fileContent = file_get_contents($arg);
    $result = [];
    $blanks = [];
    $blankGroupIndex = 0;
    $newLine = true;
    $onBlank = false;
    $pos = 0;

    foreach (str_split($fileContent) as $char) {

        if ($newLine) {
            $blanks = [];
            $blankGroupIndex = 0;
            $newLine = false;
            $pos = 0;
            $onBlank = false;
        }
        
        if ($char === ' ' || chr(9) === $char) {
            if (! $onBlank)
                $blanks[$blankGroupIndex] = 0;

            $actualBlankLen = getLen($pos, $char);
            $blanks[$blankGroupIndex] += $actualBlankLen;
            $pos += $actualBlankLen;
            $onBlank = true;
            continue;
        } elseif ($onBlank) {
            $onBlank = false;
            $result[] = [' ', $blanks[$blankGroupIndex++]];
        }

        if ($char === PHP_EOL)
            $newLine = true;

        $result[] = [$char, 1];
        $pos++;
    }

    $emacsed = '';

    foreach ($result as $charData) {
        $emacsed .= getRepresentation(0, $charData);
    }

    return $emacsed;
}

/**
 * @param int $pos
 * @param [] $charData
 * @return string
 */
function getRepresentation($pos, $charData) // -- TODO Prendre en compte les columns de emacs
{
    // 0 : char
    // 1 : len

    if ($charData[0] !== ' ')
        return $charData[0];

    $res = '';

    if ($charData[1] > 7) {
        while (($charData[1] -= 8) > 0) {
            $res .= chr(9);
        }
        $charData[1] += 8;
    }

    while ($charData[1] -- > 0)
        $res .= ' ';

    return $res;
}


/**
 * @param int $pos
 * @param string $char
 * @return int
 */
function getLen($pos, $char)
{
    if ($pos > 3)
        while (($pos -= 4) > 3) ;

    if ($char != chr(9))
        return (1);

    return [4,3,2,1][$pos];
}



//http://stackoverflow.com/questions/11267086/php-unlink-all-files-within-a-directory-and-then-deleting-that-directory
function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

?>
