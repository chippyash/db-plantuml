# PlantUML Database Support
## chippyash/db-plantuml

### What

1. Provides Plantuml !include files that you can use to create
logical and physical database diagrams
2. Provides a (PHP) utility to turn the diagrams into DDL files to create
your database.

## PlantUml Support

- Tested with PlantUML V1.2021.12 - Graphviz version 2.43.0

2 definition files
 - DatabaseLogical.iuml
 - DatabasePhysical.iuml

### Goal
To quickly create logical db designs that end users might understand
and convert them to initial physical designs with additional functionality
that can be turned into something developers will understand, i.e. a SQL Schema

### Demo

 - Open `examples\User-Logical.puml` and display the drawing
 - Open `examples\User-Physical.puml` and display the drawing

Look at both files. The only difference between them is
 - a/ the included file defined at top of definition
 - b/ physical drawing has additional features
    - View
    - Trigger & trigger()
    - (Stored) Proc and uses()

The original logical definition was copy-pasta'd from the logical file
to the physical file and renders automatically in physical form.

Take a look at the `dist\*.iuml` files. It's the subtle differences between the
function declations that allows the transform to happen.

### Installation
There is no real need to install this code base. You can access the
required files remotely using the `!includeurl` directive.

_You do however, need to install [PlantUml](http://plantuml.com/)_!

### Usage
1. Create your logical model to represent customer/user view
2. Copy logical model file to physical model file and change the !include
statement. Amend as necessary.
3. (Optional) Generate the SQL DDL file to create your database

#### Logical Models
`!include ../dist/DatabaseLogical.iuml`

or

`!includeurl https://raw.githubusercontent.com/chippyash/db-plantuml/master/dist/DatabaseLogical.iuml`

![examples/User-Logical.puml](examples/User_Logical.png)
Logical model will display the following differently to their representation
in a Physical model
##### Components
```
Table(alias, name="", description="")  //preferred
or 
Entity(alias, name="", description="") //psuedonym for Table

Type(alias, name="", description="")   //data type (enum)

Set(alias, name="", description="")    //data type (set)
```
##### Data types
```
string(l=30)
char(l=12)
text()
date()
time()
datetime()
int(l=8)
real()
bool()
```
NB string and int lengths do not display in logical models, but specify
them if you know them as they will be displayed in physical models.

You can display other data types directly in your entity classes e.g.
```
Table(t1, foo) {
    bin_data blob
}
```
##### Type modifiers
```
not_null(name, type)
unsigned(name, l=8)  //unsigned integer with a name
_unsigned(l=8)       //unsigned integer, use with primary() etc
```
##### Indexes and keys
```
primary(name = 'id', type=_unsigned(8), auto=1)  //primary key
idx(name, type=int())      //non unique index - one member
idx2(name1, name2)         //non unique index - two members
idx3(name1, name2, name3)  //non unique index - three members
```
##### Relationships
```
zeromany(from, to, verb, tNum='n')
onemany(from, to, verb, tNum='n')
manymany(from, to, verbFrom, verbTo)
oneone(from, to, verb, keyname='id', type=int())
depends(from, to, colname)  //enum and set dependencies
```
There is an internal relationship `_join`
```
_join(from, to, verb, fNum, tNum)
e.g.
_join(a, b, has, 0, n)
```
You can use this to fine tune relationships as required. fNum & tNum accept an integer
or 'n'.

#### Physicals Models
`!include ../dist/DatabasePhysical.iuml`

or

`!includeurl https://raw.githubusercontent.com/chippyash/db-plantuml/master/dist/DatabasePhysical.iuml`

![examples/User-Physical.puml](examples/User_Physical.png)

Use the same statements as per Logical models. In addition there are:
##### Components
```
Trigger(alias, name)
e.g.
Trigger(t1, UserUpdate) {
    beforeUpdate()
    afterUpdate()
    beforeInsert()
    afterInsert()
    beforeDelete()
    afterDelete()
}

Proc(alias, name)
e.g.
Proc(p1, StoredProcs) {
    addUser(uid, guid)
}

View(alias, name)
e.g.
View(v1, sessions) {
    select(user_id, data\nfrom user, session\njoin id on user_id)
}
```
##### Indexes and keys
```
foreign_key(tableName, to, type=int(), suffix='_id')
```
Foreign keys are automatically generated where appropriate between Tables
in your model. You may need to explicitly declare them for Tables that are off model.

##### Relationships
```
function triggers(from, to)  //table actions trigger
function uses(from, to)      //proc uses table
```
These `uses` relationships is purely informational.

## Diagram to SQL conversion

A PHP utility CLI program that will convert your physical diagram to SQL DDL.

MySql is supported at this release.

- Installation - "See Installation - production use" below

### Basic usage
`bin/pumldbconv g ./examples/User-Physical.puml ./out.sql`

Which will convert the example physical diagram into SQL looking thus:
```sql
CREATE TABLE `user` (
    `id` INT(8) PRIMARY KEY AUTO_INCREMENT,
    `tag` VARCHAR(30),
    `username` VARCHAR(30),
    `bar` TEXT NOT NULL,
    `password` VARCHAR(30) NOT NULL
);

CREATE TABLE `session` (
    `id` INT(8) PRIMARY KEY AUTO_INCREMENT,
    `data` text NOT NULL,
    `user_id` INT(8)
);

CREATE TABLE `account` (
    `logon` VARCHAR(30),
    `user_id` INT(8)
);

CREATE TABLE `profile` (
    `age` SMALLINT,
    `birthday` DATETIME NOT NULL,
    `id` INT(8) PRIMARY KEY,
    `gender` enum('MALE','FEMALE') NOT NULL,
    `fav_colours` set('RED','BLUE','GREEN') NOT NULL
);

CREATE TABLE `group` (
    `id` INT(8) PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL
);

CREATE TABLE `user_group` (
    `user_id` INT(8),
    `group_id` INT(8)
);

CREATE INDEX idx_att34 ON user (`tag`);

CREATE UNIQUE INDEX idx_att35 ON user (`username`);

CREATE INDEX idx_att36 ON user (`username`,`password`);

ALTER TABLE `session` ADD FOREIGN KEY fk_att40 (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

CREATE INDEX idx_att43 ON account (`logon`);

ALTER TABLE `account` ADD FOREIGN KEY fk_att44 (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

ALTER TABLE `user_group` ADD FOREIGN KEY fk_att54 (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

ALTER TABLE `user_group` ADD FOREIGN KEY fk_att55 (`group_id`)
    REFERENCES `group` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

CREATE VIEW `sessions` 
    AS SELECT user_id, data from user join session on (user.id = session.user_id);

DELIMITER //

CREATE PROCEDURE sp_StoredProcs_addUser(IN uid INT, IN guid INT)
    BEGIN
        # complete proc body and parameter typing
    END;

DELIMITER ;

CREATE DEFINER=`root`@`localhost` TRIGGER UserUpdate_beforeUpdate
    BEFORE UPDATE ON `user` FOR EACH ROW
    BEGIN
        # complete trigger body and declaration
    END;

CREATE DEFINER=`root`@`localhost` TRIGGER UserUpdate_afterUpdate
    AFTER UPDATE ON `user` FOR EACH ROW
    BEGIN
        # complete trigger body and declaration
    END;

``` 

The program assumes that your plantuml.jar is located at:
- /usr/share/plantuml/plantuml.jar for Linux
- "C:/Program Files/Java/jars/plantuml.jar" for Windows

If this is not the case, you can specify the folder location with the `-p` flag e.g.:
`bin/pumldbconv g -p /usr/local/javajars ./examples/User-Physical.puml ./out.sql`

### Installation - production use
You will need PHP8.0+ with the xsl and xml extensions installed to use this program.

- Clone/Fork this repo or grab an archive and unzip it
- Move the bin/pumldbconv file into a directory in your path, perhaps `/usr/local/bin`
- Check that you can execute it with `pumldbconv -V`
- Remove the source files if no longer required

### Installation - development
Caveat: These instructions assume a Linux OS. (If you are a Windows/Mac user, 
please consider adding installation and usage instructions to this repo by way 
of a pull request.)

- Clone/Fork this repo or grab an archive and unzip it
- Install [Composer](https://getcomposer.org/)
- Install the PHP XSL extension e.g. For Debian based Linux
```bash
sudo apt install php-xsl
```
PHP normally has the XML extension built-in, but you may need to install it manually.
```bash
sudo apt install php-xml
```
- run `composer install`

### Building
```bash
make build
```
Will build a new PHAR executable in the bin directory. You will need [Box](https://github.com/humbug/box) installed
and your `php.ini` settings modified to build phar files (off by default).

### Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

Or log an issue ticket in Github.

## Where?

The library is hosted at [Github](https://github.com/chippyash/db-plantuml). It is
available at [Packagist.org](https://packagist.org/packages/chippyash/db-plantuml)

## License
This software is licensed under the [BSD-3 Clause license](LICENSE.md).

## History
V0.0.0 Initial alpha release

V0.0.1 Alpha release with DDL generator

V1.0.0 Upgrade to use PHP 8 and latest version of PlantUML

V1.1.0 Add UNSIGNED attribute support
