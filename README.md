Wheel PHP
=========
#Global $_ variable#
| Type | Variable | Info |
|------|----------|------|
| (str)	| **$\_['version']**		 | Framework version |
| (array)	| **$\_['config']**		 | Configuration variables |
| (obj)	| **$_[‘controller’]**	 | Main controller handler |
| (obj)	| **$_[‘db’]**		       | Database handler |
| (obj)	| **$_[‘database’]**	   | Alias for database handler |
| (obj)	| **$_[‘session’]**		 | Easy-access object to session vars |
| (obj)	| **$_[‘cookie’]**		   | Easy-access object to cookies vars |
| (obj)	| **$_[‘error’]**		   | Error-system handler |

From controllers, **$_[xxx]** may be replaced by **$this->xxx**.

#Files and directories#
| x | Path | Info |
|---|------|------|
|.  | /    | The root |
|.. | /config | Contains all YAML configuration files |
|...| /config/core.yml | Some core configs |
|...| /config/databases.yml | Contains the configuration about the way your site connect to the DB |
|...| /config/render.yml | Render configuration file |
|...| /config/routes.yml | List of all the routes, and their targets |
|.. | /controllers | The directory where you put your cute controllers |
|.. | /core/* | All the magic comes deeply from this directory |
|.. | /lib/* | Some other codes WheelPHP uses |
|.. | /models | The directory where you can put your handmade models (facultative) |
|.. | /views | Heres come all the views |
|...| /views/cache | Caches for compilated templates and stylesheets |
|...| /views/layouts | You can put your main layouts there |
|...| /views/styles | You can put yours SCSS here to be auto-compiled |
|...| /views/\*Controller | Views about the controller \* |
|.. | /web | Put all your files, images, etc... there |

#Database access methods#

Foreign keys must be named like *tableName* \_ *fieldName*

##Connect to the Database##

Automatic if autoLoad configured

+ **$_[‘db’]->connect([** $databaseConfigName **] );**

##Execute SQL query directly##
+ **$_[‘db’]->** tableName **->sql(** $sql **);**

##Select##
+ **$_[‘db’]->** tableName **->select( [** $options **] );**

>	$options may contains : ‘fields’, ‘conditions’, ‘order’, limit

+ **$_[‘db’]->** tableName **->selectFirst( [** $options **] );**

>	$options may contains : ‘fields’, ‘conditions’, ‘order’

+ **$_[‘db’]->** tableName **->selectBy** Field **(** $valueOfField **[,** $options **] );**

>	$options may contains : ‘fields’, ‘order’, ‘limit’

+ **$_[‘db’]->** tableName **->selectFirstBy** Field **(** $valueOfField **[,** $options **] );**

>	$options may contains : ‘fields’, ‘order’

+ **$_[‘db’]->** tableName **(** $id **);**			*// shortcut for ->selectFirstById($id);*

##Update##
+ **$_[‘db’]->** tableName **->update(** $varName **,** $value **[,** $options **] );**

>	$options may contains ‘conditions’, ‘order’, ‘limit’

+ **$_[‘db’]->** tableName **->updateFirst(** $varName **,** $value **[,** $options **] );**

>	$options may contains ‘conditions’, ‘order’

+ **$_[‘db’]->** tableName **->updateBy** Field **(** $valueOfField **,** $varName **,** $value **[,** $options **] );**

>	$options may contains ‘order’, ‘limit’

+ **$_[‘db’]->** tableName **->updateFirstBy** Field **(** $valueOfField **,** $varName **,** $value **[,** $options **] );**

>	$options may contains ‘order’

##Delete##

+ **$_[‘db’]->** tableName **->delete( [** $options **] );**

>	$options may contains ‘conditions’, ‘order’, ‘limit’ (limit = 1 by default to prevent sh\*ts)

+ **$_[‘db’]->** tableName **->deleteBy** Field **(** $valueOfField **[,** $options **] );**

>	$options may contains ‘order’, ‘limit’ (limit = 1 by default to prevent sh*t to happends)

##New from array##
+ **$_[‘db’]->** tableName **->new(** $arrayOfValues **);**

The select methods return array containing objects (see below) or return only one object if "selectFirst"
#Database select result object#
##Get the value of field##
+ **echo $** resultObject **->get(** $fieldName **);**
+ **echo $** resultObject **->** field **;**

##Set the value of field##
+ **$** resultObject **->** field **=** $newValue **;**
+ **$** resultObject **->set(** $fieldName **,** $newValue **);**

##Delete from the database##
+ **$** resultObject **->delete();**

##Get array with all fields##
+ **$** resultObject **->rawData();**

#Error and logs#
##Select a view for errors##
+ **$_[‘error’]->view = ‘** error **’;**

##Log an error##
Stop all, just show the error.
+ **$_[‘error’]->fatal(** $errDebugDescription **[,** $userMessage **] );**

Show error, and try to continue.
+ **$_[‘error’]->error(** $errDebugDescription **[,** $userMessage **] );**

Just a warning.
+ **$_[‘error’]->info(** $errDebugDescription **);**

##Show the content of a variable##
+ **$_[‘error’]->debug(** $variable **);**

##Show the errors and log##
+ **$_[‘error’]->showErrors();**

##Flash errors for user##
Create new flash error
+ **$_[‘error’]->flash(** $userMessage **[,** $styleClass **] );**

Show all flash recieved since the last showFlash() for the user.
+ **$_[‘error’]->showFlash();**

##Add to log##
+ **$_[‘error’]->log(** $textToLog **);**

#Routing configuration#
You can add/remove/edit routes in config/routes.yml

##The generic path##
First, you define the generic path. Starting by a / You can use:
+ All alphanum and \_ \- chars
+ Slash / to separate
+ variables {myvar}
+ facultative []
+ end with ... to say 'may include some craps after'

##The destination##
You need to send the root to controller & action.

##SAMPLE##

```yaml
'/{c}/{a}/{id}[/]...':
	 controller: '{c}'
	 action: '{a}'
	 options: '{id}'
```

##Special cases (Stylesheets)##
All the .css files are redirected to SCSS compiler / cache

