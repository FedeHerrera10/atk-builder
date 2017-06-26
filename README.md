# atk-builder

## Code generator tool for the ATK Framework

### What is this?
atk-builder is a code generator tool for the ATK Framework (See https://github.com/Sintattica/atk ).
It is said than a video is worth a thousand words, so here you have a (lousy) one ...

[![Click to see video](http://img.youtube.com/vi/9N_qECiHCAg/0.jpg)](http://www.youtube.com/watch?v=9N_qECiHCAg)

### Installing

Create a new atk-skeleton project with:

```
composer project-create sintattica/atk-skeleton myproject
```

The run composer update with:

```
composer update

```

Once all the software components are updated, initialize the application with:

```
vendor\bin\atk-builder inzapp --db-name=myproject --db-user=youruser --db-passwd=yourdbpass

```

With the application initialized, you can start a local web server to serve the project with:


```
cd myproject
php -S localhost:8080 -t web

```
Now you can point your browser to http:\\localhost:8080 and enter the aplication with user `administrato` password `demo`.
After loggin in, please run the **Setup** option to create the required databases objects.

### How does it works?
The idea behind atk-builder is to write the minimun possible to have a fully functional ATK application.
An ATK application is built around modules, nodes and attributes, you normally make a folder inside the modules folders and populate that folder with classes derived from the **Node.class**, that node will correspond to a database table. In the node class you declare the table and add an attribute for every column, any time you change the table structure you have to fix the node class to reflect the changes,  the process, albeit simple, is time consuming.
Atk-builder let's you avoid a lot of work, atk-builder let's you declare a simple text file called by default **DefFile** wich contains definitions about the modules, nodes and attributes that comprises an ATK application.
After parsing this **DefFile** atk-builder will:

- Create or Drop the required tables.
- Add or Drop the required columns to the table.
- Write or Re-write the required code for the Module/s class/es and / or the node/s class/es.

A simple **DefFile** look like this:

```
appnme:myapp
db:myappdb:root:pass

module:payroll
	node:employees
		name
		date_of_birth
		salary_ammount
		notes
```

It defines the application name (myapp) the database name (myappdb) the user and password of the database (root ans pass) it defines a module (payroll) wich contains a node (employees) wich have several attributes (name, date_of_birth, salary_ammount, notes)

Running atk-builder without any arguments looks for a file called **DefFile** in the current directory, running the tool with the previous definition will:

- Create the payroll_employee tables if it not exists.
- If the table exists it will add or drop the corresponding columns in order to adjust the table to the definition.
- Will create the Module folder.
- Will create the necessary Module classes.
- Will create the necessary Node classes.

Modules and Node classes are created in pairs, a Node class for Employees will create two source files, one called Employees_base.php and another one called Employees.php, Employee extends Employee_base. Employee_base will be overwritten by
Atk-builder each time it runs a **RUNGEN** command, but Employee.php will only be created jut once, Employee.php is the place where your validations and business rules must be expressed.
The base classes will be rewritten each time the attributes list changes, attributes/columns types are infered using their names so date_of_birth will result in a column called data_of_birth with DATE type and an attribut of type DateAttribute.
Not only the type is infered on the name, the flags are infered too, the name column will have a Attribute::AF_SEARCH flag because searching for a name is an obvious requirement it will have an Attribute::AF_OBLIGATORY as well, because obviously a name is allway required.
atk-builder will try to infer as much as possible on your **DefFile** but you can fine tune the code generation i.e:

```
appnme:myapp
db:myappdb:root:pass

module:payroll
	node:employees
		name:Employe Name:Attribute:AF_OBLIGATORY, 30
		date_of_birth
		salary_ammount
		notes
```

The modified line :

```
name:Employe Name:Attribute:AF_OBLIGATORY, 30
```

Means that the name column label in forms will be **Employee Name** it will be rendered as a simple attribute with a width of 30 chars and it will have the AF_OBLIGATORY flag set.

### How do I write a DefFile ?

The full syntax of the **DefFile** is as follows:

```
appnme:myapp
db:myappdb:root:pass

module:payroll
	node:employees:[label]:[actions]:[node_flags]:[show_in_menu]
		name:[label]:[attribute_type]:[attribute_flags]:[tab]		
```

The items between square brackets are optional, if ommited, proper values will be infered for them based on context.
A brief discussion about the parameters follows:

- label: If ommited the name for the Module/Node/Attribute will be used as label form Menus and/or form label.
- actions: These are the registered actions for the node, if ommitted the standard action (admin, add, update, view and delete) will be used, if you need a non standard action, you have to declare all the standard action too if you need them, i.e. if you need a print_orde action besides the standard ones, declare **admin, add, update, view, print_order**
- node_flags: The node flags you want for this node (see vendor/sintattica/atk9/src/Node.class)
- show_in_menu: If this node should be shown in the menu, use false here for details nodes in master details setup.
- attribute_type: The attribute type for the attribute, if left blank the type will be infered by Atk-Builder.
- attribute_flags: The flags for the attribute, if left blank Atk-Builder will infer the proper flags based on the attribute name.
- tab: if provided, the attribute will be shown in a the tab named in the parameter, if left blank the attribute will go in the main tab.

### So far, so good ...so what do I do next?

After you have created you new application with:

```
atk-builder.phar --newapp MyApp -u user -p password -b /path/for/my/new/app
```

You must edit the provided **DefFile** and Add some Modules, Nodes and attributes definitions into it, then run: 

```
atk-builder.phar
```

Without arguments, that will trigger the **rungen** command wich will update everything, Modules, Nodes and tables,
then you edit your **DefFile** again, add some Modules, Nodes or attributes or remove them as well, and run the generarion again.
While you are at that, you can edit the non base classes to add validation and business logic.
A common session will look like:

```
atk-builder.phar --newapp MyApp -u user -p password -b /path/for/my/new/app
cd /path/for/my/new/app
vim DefFile //Add some defs
atk-builder.phar
php -s 0.0.0.0:8000 -t web //Check them out
vim DefFile //Add some more, update old defs
atk-builder.phar //Update the system
php -s 0.0.0.0:8000 -t web //Check it out
vim src/Module/SomeNode.class //Add some business logic to a node
php -s 0.0.0.0:8000 -t web //Test it
vim DefFile //Add some more, update old defs
atk-builder.phar //Update the system
.
.
.
```

Wash, rinse and repeat until your app is good enough for prime time.
