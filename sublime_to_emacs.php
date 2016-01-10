<?php

$argv1 = $argc >= 2 ? $argv[1] : null;
$argv2 = $argc >= 3 ? $argv[2] : null;


// ---------------------------- SETTINGS -------------------------

$JAI_CONFIGURE_LES_PARAMETRES = false;
// - At true when configuration done
                
// Dossiers source et final, chemin relatif ou absolut. Sur-écrit par argv[1] et [2]
$baseDirectory  = $argv1 ?: '/Users/Nico/softwar' . '/';  
$finalDirectory = $argv2 ?: '/Users/Nico/softwar/CLEANED3' . '/';
// - Source and Final directories, absolute or relativ path. Overwritten by argv[1] and [2]


// Masque des fichiers à ré-indenter
$selectionMask  = ['.c', '.h'];
// - Only files corresponding to this mask will be re-indented


// Option pour ne copier dans le dossier final que les fichiers répondant aux masques plus bas
// Si false, on recopiera donc les même fichiers que dans le repertoire source
// Utile pour clean les merdes qu'on a souvent genre les .o, ~ etc. 
$preciseCopy = true; 
// - If you only want to copy certain files, put this at true then fill the below array

$preciseCopyMask = ['Makefile', '.c', '.h']; // Osef si $preciseCopy == false
// AU FAIT les .svn .git et autres .DS_STORE ne sont pas copié de base.
// - BTW .svn .git .DS_STORE and other bs aren't copied by default


// ---------------------------- END SETTINGS ---------------------------- 
// - If u encounter an error message, read the according comment to translate


if (! $JAI_CONFIGURE_LES_PARAMETRES) {
    die("Merci de configurer les params au tout début du script" . PHP_EOL);
    // - Put $JAI_CONFIGURE_LES_PARAMETRES at true
}

if ($baseDirectory === $finalDirectory) {
    die("L'overwriting n'est pas autorisé" . PHP_EOL);
    // - Overwriting not allowed, for ur safety. Change the finalDirectory location
}

if (file_exists($finalDirectory)) {
    die("Merci de choisir un dossier final non existant" . PHP_EOL);
    // - Ur final directory already exists, use another
} 

if (! file_exists($finalDirectory))
    mkdir($finalDirectory, 0777, true);

echo "Sublime to emacs -->  " . realpath($baseDirectory) . " TO " . realpath($finalDirectory) . PHP_EOL;

main($baseDirectory, $finalDirectory, $selectionMask, $preciseCopy, $preciseCopyMask, $overwrite);

echo "Succes";

/**
 * @param string $baseDirectory
 * @param string $finalDirectory
 * @param array $selectionMask
 * @param bool $preciseCopy
 * @param array $preciseCopyMask
 * @param bool $overwrite
 * @throws Exception
 */
function main($baseDirectory, $finalDirectory, $selectionMask, $preciseCopy = false, $preciseCopyMask = [], $overwrite = false)
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

?>
