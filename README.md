# sublime-indent-correcter
If your `cat` from Sublime2 projects looks horrible, this may be for you.

## What is this
When coding on sublime-text2, or other text-editors / IDE, you often get an indentation interpretation that is different from native one.
If you are not able to see ur files nicely on your terminal, this may help you.

It will copy a given directory, to another new one, re-indenting the files you want.

## *Francais: Cas d'utilisation principal*
*En école d'ingé on vous demande de suivre une norme comprenant des règles d'indentations. Et cette norme ne sera pas vérifiée sous votre editeur mais bien sur le terminal. Bah si vous avez codé sous sublime, le résultat sera déguelasse*


## Speaking-his-self-example

![Super-example image](http://image.noelshack.com/fichiers/2016/01/1452448315-sublime-indent-correcter.png)

## How to use

  1.  Read [Warnings](#warnings) chapter.

  2.  Clone it: `git clone https://github.com/NicolasCharpentier/sublime-indent-correcter`
  
  2.  **Open** up `sublime_to_emacs.php` and **configure it**.
      
      **`$toIndent`** : Array of file masks, to indent *(in the copy result)*.
          
        ex. ['.c', '.h']
        
        (It's a simple strpos on fileNames, so beware, george.c.py will be re-indented, but who does that)
      
      **`$copyOnlyTheseFiles`** : Array of file masks, to copy to new directory. null if you want every file copied.
      
        ex. null
            ['Makefile', '.c', '.h']
  
  3.  Launch it. `php sublime_to_emacs.php /path/to/my/current_project /path/to/my/new_cleaned_project`

## Warnings
  This does not magically guess the correct indentation, but converts it the way you see it in Sublime.
  
  This **ONLY** have been tested from files created with the sublime-text-2 default config (indent width: 4).
  
  *"My project has mixed files, some from sublime, some from emacs"* You should write the filenames from Subl in `$toIndent` (instead of the usual wildcards like .c)
  
## Curious?  
  Sublime with **default conf** will interpret every tab as a 1 to 4 spaces, depending on the column your at.
  
  **Starting from 0**, placed at column 2, a tab will display 2 spaces (col 2, col 3).
  
  At **each (multiple of 4) - 1**, you can imagine a **tab-line**, for each column, each tab will reach their next **tab-line**.  
  
                                                                          (cant break line lol)
  
  
  Native implementation, is 8 columns for a tab. With the same system described higher, just changing to 8.
  
  So your tab at col 0 on sublime which displays 4 spaces, will do 8 on emacs/cat.
  
  The script will convert this tab to 4 spaces. If your sublime displays 8 spaces, the script will use a tab caracter and not 8 spaces.
  
  It is not complex, you can reproduce this at home. 
  
## TODOS etc.
  Probably nothing will change. 
  
  However you are free to ask for modifications or contribute.
  
  Any sort of feedback will be listened
