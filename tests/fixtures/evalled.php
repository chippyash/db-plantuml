<?php

use Chippyash\Schema\PumlSchema;
use Chippyash\Schema\PumlTables;
use Chippyash\Schema\PumlTable;
use Chippyash\Schema\PumlViews;
use Chippyash\Schema\PumlView;
use Chippyash\Schema\PumlTypes;
use Chippyash\Schema\PumlType;
use Chippyash\Schema\PumlTriggers;
use Chippyash\Schema\PumlTrigger;
use Chippyash\Schema\PumlProcs;
use Chippyash\Schema\PumlProc;
use Chippyash\Schema\PumlRelationships;
use Chippyash\Schema\PumlRelationship;
use Chippyash\Schema\PumlNode;

$tables = new PumlTables();
$views = new PumlViews();
$types = new PumlTypes();
$procs = new PumlProcs();
$triggers = new PumlTriggers();
$relationships = new PumlRelationships();

$tables->addTable(
    (new PumlTable(
        "cl0002", "user"
    ))
        ->addAttribute("att29", "id INT[8] PRIMARY KEY AUTOINCREMENT"
        )->addAttribute("att30", "tag VARCHAR[30]"
        )->addAttribute("att31", "username VARCHAR[30]"
        )->addAttribute("att32", "bar TEXT NOT NULL"
        )->addAttribute("att33", "password VARCHAR[30] NOT NULL"
        )->addOperation("att34", "index(tag)"
        )->addOperation("att35", "key(username)"
        )->addOperation("att36", "index(bar, password)"
        )
);

$tables->addTable(
    (new PumlTable(
        "cl0003", "session"
    ))
        ->addAttribute("att37", "id INT[8] PRIMARY KEY AUTOINCREMENT"
        )->addAttribute("att38", "data text NOT NULL"
        )->addAttribute("att39", "user_id INT[8]"
        )->addOperation("att40", "fk(user_id, user)"
        )
);

$tables->addTable(
    (new PumlTable(
        "cl0005", "account"
    ))
        ->addAttribute("att41", "logon VARCHAR[30]"
        )->addAttribute("att42", "user_id INT[8]"
        )->addOperation("att43", "index(logon)"
        )->addOperation("att44", "fk(user_id, user)"
        )
);

$tables->addTable(
    (new PumlTable(
        "cl0007", "profile"
    ))
        ->addAttribute("att45", "age SMALLINT"
        )->addAttribute("att46", "birthday DATETIME NOT NULL"
        )->addAttribute("att47", "id INT[8] PRIMARY KEY"
        )->addAttribute("att48", "gender gender NOT NULL"
        )->addAttribute("att49", "fav_colours fav_colours NOT NULL"
        )
);

$tables->addTable(
    (new PumlTable(
        "cl0009", "group"
    ))
        ->addAttribute("att50", "id INT[8] PRIMARY KEY AUTOINCREMENT"
        )->addAttribute("att51", "name VARCHAR[20] NOT NULL"
        )
);

$tables->addTable(
    (new PumlTable(
        "cl0010", "user_group"
    ))
        ->addAttribute("att52", "user_id INT[8]"
        )->addAttribute("att53", "group_id INT[8]"
        )->addOperation("att54", "fk(user_id, user)"
        )->addOperation("att55", "fk(group_id, group)"
        )
);

$types->addEnum(
    (new PumlType(
        "cl0013", "Gender"
    ))
        ->addAttribute("att56", "MALE"
        )->addAttribute("att57", "FEMALE"
        )
);

$types->addSet(
    (new PumlType(
        "cl0018", "Colour"
    ))
        ->addAttribute("att58", "RED"
        )->addAttribute("att59", "BLUE"
        )->addAttribute("att60", "GREEN"
        )
);

$views->addView(
    (new PumlView(
        "cl0023", "sessions"
    ))
        ->addOperation("att61", "select(user_id, data from user\njoin session on\n(user.id = session.user_id))"
        )
);

$triggers->addTrigger(
    (new PumlTrigger(
        "cl0024",
        "UserUpdate"
    ))
        ->addOperation("att62", "beforeUpdate()"
        )->addOperation("att63", "afterUpdate()"
        )
);

$procs->addProc(
    (new PumlProc(
        "cl0026",
        "StoredProcs"
    ))
        ->addOperation("att64", "addUser(IN uid INT, IN guid INT)"
        )
);

$relationships->addRelationship(
    (new PumlRelationship("ass65", "will have"))
        ->addNode(new PumlNode("cl0002"))
        ->addNode(new PumlNode("cl0003"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass68", "may have"))
        ->addNode(new PumlNode("cl0002"))
        ->addNode(new PumlNode("cl0005"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass71", "has a"))
        ->addNode(new PumlNode("cl0002"))
        ->addNode(new PumlNode("cl0007"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass74", "can be in"))
        ->addNode(new PumlNode("cl0002"))
        ->addNode(new PumlNode("cl0010"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass77", "can have"))
        ->addNode(new PumlNode("cl0009"))
        ->addNode(new PumlNode("cl0010"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass80"))
        ->addNode(new PumlNode("cl0015"))
        ->addNode(new PumlNode("cl0013"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass83", "gender"))
        ->addNode(new PumlNode("cl0007"))
        ->addNode(new PumlNode("cl0013"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass86", "fav_colours"))
        ->addNode(new PumlNode("cl0007"))
        ->addNode(new PumlNode("cl0018"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass89"))
        ->addNode(new PumlNode("cl0018"))
        ->addNode(new PumlNode("cl0021"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass92"))
        ->addNode(new PumlNode("cl0002"))
        ->addNode(new PumlNode("cl0024"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass95"))
        ->addNode(new PumlNode("cl0026"))
        ->addNode(new PumlNode("cl0002"))
);
$relationships->addRelationship(
    (new PumlRelationship("ass98"))
        ->addNode(new PumlNode("cl0026"))
        ->addNode(new PumlNode("cl0009"))
);
$schema = new PumlSchema(
    $tables,
    $views,
    $types,
    $procs,
    $triggers,
    $relationships
);
        