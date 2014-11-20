<?php
/*
In fact, in web application, it’s better not to have two urls respond to the same thing. But we are a lazy programmer, right, RIGHT ? We don’t have a much time to remember the route where is end with “/” or not.
For example we have 2 url like this :
http://zf-tutorial/album/add – 200 – works fine
http://zf-tutorial/album/add/ – 404 – The requested URL could not be matched by routing.
*/
//To handle that, just add the empty “/” in the end of route segment :
'route'    => '/[:controller[/[:action[/]]]]',
		
//Or for “:slug” segment :
'route'    =>  '/[:controller[/[:action[/[:slug[/]]]]]]',
		
//I do not know if this is a best solution or not, but this is probably one of the easiest.