Structure is a file based chunk helper for modx revolution 

What is Structure  
******************

Structure is an helper class that will allow you to use filebased chunks in a different manner than static elements.

What was the problem to begin with ?
------------------------------------

Static elements already allow any MODx Element to be filebased (CHunks, Snippets, Plugin, Template) which is a much awaited feature.
But there are several point that still keep me from using them :
- You can't create filebased elements on the fly, you must create the Element in the manager and set the path from there, and tick an option to tell modx that it is filebased.
- If you're in development, you need a plugin to refresh the modx cache if you want the elements to be synched with your last modifications on each page reload.

What do you want to achieve ?
------------------------------

- I want to use on the fly filebased chunks
- I dont want to use the manager to tell anything to modx
- I want modx to assume some settings (paths...) if i don't have the need to override them

So what does structure do actually ?
------------------------------------

Structure allow me to let the templating be more "dynamic". 
To achieve that goal, structure comes bundled with an helper snippet called tpl.
This snippet will allow me to use on the fly chunks.

Let's say that i have a template called "MyTemplate" loacted in the common default template directory (yoursite/assets/templates/)

in this template if i put [[+mychunkname:tpl]]

Structure will search for a "tpl" file in "yoursite/assets/templates/mytemplate/structure/mychunkname.tpl"
If it does find it, it's going to be processed and the html returned, otherwise il return an error message with the path where it attempted to get the file (this will be a debug option later)

you can even use subdirectories [[+subdir/myotherchunk:tpl]]

This so not modx, what if i don't want to use the default path, what about the options ?
----------------------------------------------------------------------------------------

Structure uses Template Properties to communicate with the current selected template
Therefore, if you have a template named "My Awesome Template" in the manager but loacted in "assets/templates/mytheme"

Just add a Template Property called "template_name" as a textfield with the value of "mytheme" and structure
will be able to get you chunk in the right directory.

I still don't want to use the common template path...
-----------------------------------------------------

Your content your way is a mojo with modx, and structure take that in consideration.
You can add other Template properties to set your own path:

- template_path : the path to template (default to : "/assets/templates/")
- structure_dir : directory where all tpls are loacted (default to "structure/")

Note that those two setting have a trailing slash. You've been warned.

Seems nice, is there anything more that i should know ?
-------------------------------------------------------

Oh yes there is.
Structure can listen for some Resource class_keys, allowing you to override some placeholders with another filesbased chunks.
That's another story, coming in the next episode


Limitations
************

For now, il only work for chunk for which you don't pass any additionnal options and it will not work with snippets calls.
Eventually, Structure will have a method that other snippets who want to use it will be able to take advantage of. But most of the popular ones will not.

You can use it with TV but you can't use it like an output filter because the modx parser do not like it.
With TV you should use the following syntax:

[[tpl? &name=`[[*myTv]]`]]


Requirements
*************

- MODx Revolution 2.2