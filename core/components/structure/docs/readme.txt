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
- I don't want to be limited to a hardcoded directory structure

So what does structure do actually ?
------------------------------------

Structure allow me to let the templating be more "dynamic". 
To achieve that goal, structure comes bundled with an helper snippet and output filter called "tpl".
This snippet will allow me to use on the fly chunks.

Let's talk about a usage case.
Usually, most modx templates are located in "{root}/assets/templates/{template_dir}".
Let's say that i have a template called "MyTemplate" "{root}/assets/templates/mytemplate/"

I can put anywhere in my template (or in chunks that is using the template) the following placeholder
[[+mychunkname:tpl]]

Structure will search for a "tpl" file in "{root}/assets/templates/mytemplate/structure/mychunkname.tpl"
If the file is found, it's going to be processed and the html result returned.
On the other hand, if the file is not found, Structure returns an error message with the tpl file name and path where it attempted to get the file from (this will be a debug option later).

It's also possible to use subdirectories [[+subdir/myotherchunk:tpl]] convert to : "{root}/assets/templates/mytemplate/structure/subdir/myotherchunk.tpl"

This is not really a modx way to work, what if i don't want to use the default path, what about the options ?
-------------------------------------------------------------------------------------------------------------

Structure uses Template Properties to communicate with the current selected Template.
Therefore, if you have a template named "My Awesome Template" in the manager but located in "{root}/assets/templates/mytheme", you can override the template directory assumed by Structure.
Jus add a template property (textfied)
- template_name : the template directory name (default to: template name lower cased)


I still don't want to use the common template path...
-----------------------------------------------------

You can override paths as well, with the following Template properties:

- template_path : the path to template (default to : "{root}/assets/templates/")
- structure_dir : directory where all tpls are located (default to "structure/")

Note that those two settings have a trailing slash. You've been warned.

Default path looks like this:

- template_path = "{asset_path}/templates/"
- structure_path = {template_path}{theme_name}{structure_directory}{placeholder}{tpl_suffix}

Yep, you can also change the tpl_suffix if you don't want to use a ".tpl" extension for your files.


Seems nice, is there anything more that i should know ?
-------------------------------------------------------

Indeed!

Structure can listen for some Resource class_keys, allowing you to override some placeholders with another filesbased chunks.
This does not sounds easy at all.
Let's take an example :

In your template, you use the following placeholder [[+content:tpl]] (converted to "{root}/assets/templates/{theme_name}/structure/content.tpl");
But when using the CRT Articles, you want this placeholder to use the filebased chunk names content-article-list.tpl in the same directory.

That's where it's getting tricky, you need to set 3 Template Properties in the template used with Articles & using Structure.
- Add a template property called "class_keys" and as value "ArticlesContainer"
- Add a template property called "articlescontainer" and as value "content"
- Add a template property called "content" and as value "content-article-list"

You tell structure to add some listeners to modResource with class_key "ArticlesContainer", and to replace the chunk name "content" by "content-article-list".

However, there is clear limitation to this method. What if you also want to replace the "content" file name for "Articles" class_key ?

That's why it's planned to allow Templates to provide a config class letting you do the magic wihtout having to dabble to much with Template Properties.


Quick Wrap Up
-------------

Any template can use Structure without having to touch the Template Properties as long as you use the predefined paths used by structure
Just create a template and drop [[+index:tpl]] in it.
Structure wil just assume that "index.tpl" is located in "{root}/assets/templates/{theme_name}/structure/index.tpl"


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