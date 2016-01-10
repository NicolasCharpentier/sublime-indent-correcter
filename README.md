# sublime-indent-correcter
If your `cat` from Sublime projects looks horrible, this may be for you.


## *Frenchies: Cas d'utilisation principal*
*En école d'ingé on vous demande de suivre une norme comprenant des règles d'indentations. Et cette norme ne sera pas vérifié sous votre editeur mais bien sur le terminal. Bah si vous avez codé sous sublime, le résultat sera déguelasse*

## What is this
When coding on sublime-text2, or other text-editors / IDE, you often get an indentation interpretation that is different from native one. This doesnt have consequences when coding on your favorite t-e/IDE, but try to `cat` some file, you'll probably end-up coming back here

## Speaking-his-self-example

![Super-example image](http://image.noelshack.com/fichiers/2016/01/1452448315-sublime-indent-correcter.png)

## How to use

  1.  Read *Warnings* chapter.

  2.  Get the .php file by cloning the repo or whatever.
  
  2.  Open up the file and configure it by reading the comments.
  
  3.  Launch it, optionally using arguments
  
  `php sublime_to_emacs.php` || `php sublime_to_emacs path/to/my/project path/to/my/cleanedproject`

## Warnings
  This does not magically guess the correct indentation, but converts it the way you see it in Sublime.
  
  This **ONLY** have been tested from files created with the sublime-text-2 default config (indent width: 4).
  
## TODOS etc.
  Probably nothing will change. 
  
  However you are free to ask for modifications or contribute.
  
  Any sort of feedback will be listened
