# sublime-indent-correcter
If your `cat` from Sublime2 projects looks horrible, this may be for you.

## *Francais: Cas d'utilisation principal*
*En école d'ingé on vous demande de suivre une norme comprenant des règles d'indentations. Et cette norme ne sera pas vérifié sous votre editeur mais bien sur le terminal. Bah si vous avez codé sous sublime, le résultat sera déguelasse*

## What is this
When coding on sublime-text2, or other text-editors / IDE, you often get an indentation interpretation that is different from native one. If you are not able to see ur files nicely on your terminal, try this.

## Speaking-his-self-example

![Super-example image](http://image.noelshack.com/fichiers/2016/01/1452448315-sublime-indent-correcter.png)

## How to use

  1.  Read [Warnings](https://github.com/NicolasCharpentier/sublime-indent-correcter#warnings) chapter.

  2.  `git clone https://github.com/NicolasCharpentier/sublime-indent-correcter`
  
  2.  Open up `sublime_to_emacs.php` and configure it by reading the comments.
  
  3.  Launch it. `php sublime_to_emacs.php /path/to/my/current_project /path/to/my/cleaned_project`

## Warnings
  This does not magically guess the correct indentation, but converts it the way you see it in Sublime.
  
  This **ONLY** have been tested from files created with the sublime-text-2 default config (indent width: 4).
  
  *My project has mixed files, some from sublime, some from emacs* You should write the filenames from Subl in the `$selectionMask` (instead of the usual wildcards like .c)
  
## TODOS etc.
  Probably nothing will change. 
  
  However you are free to ask for modifications or contribute.
  
  Any sort of feedback will be listened
