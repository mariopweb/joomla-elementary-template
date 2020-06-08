# Joomla! Elementary template
This is a Bootstrap 4 style, responsive Joomla! template with integrated features like:
* drop-down menu (Bootstrap 4 / SmartMenus)
* RTL style support (Bootstrap 4)
* dedicated template positions to support responsive left and right menus
## Joomla! core overrides (HTML folder):

Components:

Modules:

Plugins:
## Menu style
<br /> Menus can be styled with the use of a Menu Class Suffix present in the module's parameters ("Module settings > Advanced"):
<br /> Use bootstrap 4 classes depending on the menu style you expect:
* Horizontal menu: put in ' navbar-nav mr-auto'
* Vertical menu: put in ' navbar-nav flex-column nav-pills'
##### RTL style
* Horizontal menu: put in ' navbar-nav mr-auto sm-rtl'
* Vertical menu: put in ' navbar-nav flex-column nav-pills sm-rtl'

**Important**: when applying these suffixes pay your attention to the leading blank space.

## Module style
<br />Modules are embed inside Bootstrap card component, so they can be styled freely with appropriate classes.

<br />**For styling the module (title + content):**
<br />use Module Class Suffix present in the module's parameters ("Module settings > Advanced")
<br />**For styling the header itself:**
<br />use Header Class present in the module's parameters ("Module settings > Advanced").

For example:
If you want your module (header + content) to have: backgroundcolor: blue & text-color: white & border:0
use: bg-primary text-white border-0

If you want your module title to have: backgroundcolor: grey & text-color: black
use: bg-light text-dark

**Important**: when typing the classes inside the appropriate fields pay your attention to the leading blank space.

More about card component and classes you can use:
https://getbootstrap.com/docs/4.5/components/card/#card-styles

You can also use a template theme leading color:
For this use a class related to the color: 
* 'bg-green'
* 'bg-red'

## Template Parameters
* Site name
* Descrition
* Theme color
## Language Support
Right now the following languages for the Joomla backend are supported in this template:
* English (en-GB)
* Polish (pl-PL)
## Demo of this template

## Download the latest release

## Resources
##### The template contains the following libraries

* Bootstrap v4.4.1 - https://getbootstrap.com
* SmartMenus jQuery - https://www.smartmenus.org/
* Font Awesome 4.7.0 by @davegandy - http://fontawesome.io
* Popper.js 1.16.0 - https://popper.js.org/
## Template launch reasons 
The inspiration to bring that template to life was other Boostrap4 based theme I had found when have been trying to find some SIMPLE template to use on mywebsite. You can find it here:

https://github.com/sniggle/joomla-bootstrap4-template

Simple becuase.. I needed something basic, elementary. Like Joomla! core Protostar, but with some newest technologies. Not some 'Powered by X Framework' like templates. Yes, there are great, but to set up a basic website there are just ... too confusing and time consuming.

You could now ask "Why didn't just contribute to that Boostrap4 template project"
The answer is that I just wated to create something on one's own and learn something new on my way to .. learn coding and .. maybe find a new job... 

Anyway it's just my hobby right now. 
