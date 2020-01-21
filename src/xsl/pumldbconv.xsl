<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : pumldbconv.xsl
    Created on : 07 January 2020
    Author     : Ashley Kitson
    Copyright  : Ashley Kitson, UK, 2020
    License    : BSD2.0
    Description:
        Transform plantuml output .xmi to normalised format
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
                xmlns:UML="href://org.omg/UML/1.3">
    <xsl:output method="text" version="1.0" encoding="UTF-8" indent="no"/>
    <!-- Overide default template rule - do not show unmatched text -->
        <xsl:template match="text()"/>
    <!-- match the overall document - starting point -->
    <xsl:template match="/">
        <xsl:text>
<!--            &lt;?php-->
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
        </xsl:text>
        <!-- find the Stereotypes -->
        <xsl:apply-templates select="//UML:Stereotype"/>
        <!-- find relationships -->
        <xsl:apply-templates select="//UML:Association"/>
        <xsl:text>
            $schema = new PumlSchema(
                $tables,
                $views,
                $types,
                $procs,
                $triggers,
                $relationships
            );
        </xsl:text>
    </xsl:template>

    <!-- Match all the Classes with 'Table' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='Table']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $tables->addTable(
                (new PumlTable(
        </xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
        <xsl:text>
                ))
        </xsl:text>
            <xsl:for-each  select=".//UML:Attribute">
                <xsl:call-template name="attribute"/>
            </xsl:for-each>
            <xsl:for-each  select=".//UML:Operation">
                <xsl:call-template name="operation"/>
            </xsl:for-each>
        <xsl:text>
            );
        </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Match all the Classes with 'View' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='View']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $views->addView(
                (new PumlView(
        </xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
            <xsl:text>
                ))
        </xsl:text>
            <xsl:for-each  select=".//UML:Operation">
                <xsl:call-template name="operation"/>
            </xsl:for-each>
            <!--            <xsl:apply-templates select=".//UML:Attribute" name="attribute"/>-->
            <xsl:text>
            );
        </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Match all the Classes with 'Enum' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='Enum']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $types->addEnum(
                (new PumlType(
        </xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
            <xsl:text>
                ))
        </xsl:text>
            <xsl:for-each  select=".//UML:Attribute">
                <xsl:call-template name="attribute"/>
            </xsl:for-each>
            <xsl:text>
            );
        </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Match all the Classes with 'Set' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='Set']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $types->addSet(
                (new PumlType(
        </xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
            <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
            <xsl:text>
                ))
        </xsl:text>
            <xsl:for-each  select=".//UML:Attribute">
                <xsl:call-template name="attribute"/>
            </xsl:for-each>
            <xsl:text>
            );
        </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Match all the Classes with 'Proc' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='Proc']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $procs->addProc(
                (new PumlProc(
        "</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",
        "</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"
            ))
        </xsl:text>
        <xsl:for-each  select=".//UML:Operation">
            <xsl:call-template name="operation"/>
        </xsl:for-each>
            <xsl:text>
            );
            </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Match all the Classes with 'Trigger' Stereotype   -->
    <xsl:template match="UML:Stereotype[@name='Trigger']">
        <xsl:for-each select="parent::node()/parent::node()">
        <xsl:text>
            $triggers->addTrigger(
                (new PumlTrigger(
            "</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",
            "</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"
            ))
        </xsl:text>
        <xsl:for-each  select=".//UML:Operation">
            <xsl:call-template name="operation"/>
        </xsl:for-each>
            <xsl:text>
            );
        </xsl:text>
        </xsl:for-each>
    </xsl:template>

    <!-- Add relationships -->
    <xsl:template match="UML:Association">
        <xsl:text>
        $relationships->addRelationship(
            (new PumlRelationship("</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>"</xsl:text>
            <xsl:if test="@name"><xsl:text>, "</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text></xsl:if>
        <xsl:text>))</xsl:text>
        <xsl:for-each select=".//UML:AssociationEnd">
            <xsl:text>
                ->addNode(new PumlNode("</xsl:text><xsl:value-of select="attribute::type"/><xsl:text>"))
            </xsl:text>

        </xsl:for-each>
        <xsl:text>);</xsl:text>
    </xsl:template>

    <!-- Add column information to table/type -->
    <xsl:template name="attribute" match="UML:Attribute">
        <xsl:text>->addAttribute(</xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
        <xsl:text>
            )</xsl:text>
    </xsl:template>


    <!-- Add operation information to view/proc/trigger -->
    <xsl:template name="operation" match="UML:Operation">
        <xsl:text>->addOperation(</xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::xmi.id"/><xsl:text>",</xsl:text>
        <xsl:text>"</xsl:text><xsl:value-of select="attribute::name"/><xsl:text>"</xsl:text>
        <xsl:text>
            )</xsl:text>
    </xsl:template>
</xsl:stylesheet>
