#PROTE FRAMEWORK.

- Named Prote as in Prototype (Also, was watching K-pax while coding this)
- This framework is now obsolete. 
- opensourced . If interested, you may build upon or use it for your own projects.
- *New version* is much more simplified and modern .(link to be updated here soon)
- No tests were ever written for this framework but Prote has served several projects(some still running without any problems). Prote is built with "fail always than fail sometimes" in mind. 
- prote relies heavily on its DIC container. Access to DIC is provided globally, hence every service is available everywhere. Helps prototyping quickly but security is also taken care of

Prote does one thing really well: *Throw away prototyping.* 
It has an in built composable user management system with installation codes directly built into its core.
only mysql database is completely supported and the user system is built with 3NF. 
support for other databases can be added trivially. 

# Directory Structure & FAQ
- /Engine contains engines for running the framework. default included is prote.
- /Routes contains routers
- /Views contains your web views
- Where are my models? 
Models exist inside specific libaries under /Engine/Prote/Lib. 
- Models are not given much flexibility in prote, This helps you write code that always works + that forces you to reuse as much as possible. 
- Where are configs? 
The underlying configuration files are under /Engine/Prote/Etc (yes, prote follows unix like structure). but overview of configs is done in /Engine/Prote/Zed.php



# Installation
copy paste and go.
- Add Database credentials in /Engine/Prote/Zed.php 
- Prote has no dependencies. 
- Prote Does not support composer. 




